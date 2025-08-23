<?php
require_once __DIR__ . '/utils/php/numToAlpha.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("localhost", "root", "", "1_dps");
    if ($conn->connect_error)
        exit(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));

    $selectedDate = $_POST['selected_date'] ?? null;

    $where = "";
    $params = [];
    $types = "";

    if (!empty($selectedDate)) {
        $where .= "WHERE IMPORT_DATE = ?";
        $params[] = $selectedDate;
        $types .= "s";
    }

    $sql = "SELECT MAX(IMPORT_DATE_TIME) AS latest_import_datetime 
        FROM delivery_plan_tb $where";
    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $latestImportDatetime = $row['latest_import_datetime'];

    if (empty($selectedDate) && $latestImportDatetime) {
        $selectedDate = date("Y-m-d", strtotime($latestImportDatetime));
    }

    $sql = "
        WITH ranked_rows AS (
            SELECT
                dp.*,
                ROW_NUMBER() OVER (
                    PARTITION BY dp.PART_NUMBER, dp.PLAN_DATE
                    ORDER BY dp.IMPORT_TIME DESC
                ) AS rn
            FROM delivery_plan_tb dp
            WHERE dp.IMPORT_DATE = ?
        )
        SELECT
            r.RID,
            r.CUSTOMER,
            r.PART_NUMBER,
            r.ITEM_NAME,
            r.REFERENCE,
            r.LOCATION,
            r.BACKLOG,
            r.PLAN_DATE,
            r.PLAN_QTY,
            COALESCE(fs.TOTAL_QTY, r.FG) AS FG,
            r.IMPORT_DATE_TIME,
            r.IMPORT_DATE,
            r.IMPORT_TIME
        FROM ranked_rows r
        LEFT JOIN fg_stock_tb fs
            ON fs.ITEM_CODE = r.PART_NUMBER
            AND fs.PROD_DATE = r.IMPORT_DATE
        WHERE r.rn = 1
        ORDER BY r.PART_NUMBER, r.PLAN_DATE;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selectedDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $deliveryData = [];
    $deliveryDataNegativeBalance = [];
    $lastPlanDate = null;

    while ($row = $result->fetch_assoc()) {
        $customer = $row['CUSTOMER'];
        $partNumber = $row['PART_NUMBER'];
        $itemName = $row['ITEM_NAME'];
        $planDate = $row['PLAN_DATE'];
        $planQtyRaw = $row['PLAN_QTY'];

        $compositeKey = $customer . '_' . $partNumber . '_' . $itemName;

        if (!isset($deliveryData[$compositeKey])) {
            $deliveryData[$compositeKey] = [
                'a' => $row['RID'],
                'b' => $customer,
                'c' => $partNumber,
                'd' => $itemName,
                'e' => $row['REFERENCE'],
                'f' => $row['LOCATION'],
                'g' => ($row['BACKLOG'] == 0 ? "-" : (int) $row['BACKLOG']),
                '_plan_dates' => [],
                '_start_fg' => (int) $row['FG'],
                '_start_backlog' => (int) $row['BACKLOG'],
            ];
        }

        $planQty = ($planQtyRaw === null || $planQtyRaw === "" ? 0 : (int) $planQtyRaw);
        if ($planDate != '0000-00-00') {
            $deliveryData[$compositeKey]['_plan_dates'][$planDate] = $planQty;
        }
        $lastPlanDate = $planDate;
    }

    if (!empty($deliveryData)) {

        foreach ($deliveryData as &$item) {
            ksort($item['_plan_dates']);

            $fg = $item['_start_fg'];
            $backlog = $item['_start_backlog'];

            $balances = [];
            $firstLoop = true;
            $previousCalc = 0;

            foreach ($item['_plan_dates'] as $date => $planQty) {
                $planQty = max(0, $planQty);

                if ($firstLoop) {
                    $calc = $fg - ($backlog + $planQty);
                    $firstLoop = false;
                } else {
                    $calc = $previousCalc - $planQty;
                }

                if ($calc > 0) {
                    $fg = $calc;
                    $backlog = 0;
                    $balances[$date] = $fg;
                } else {
                    $backlog = abs($calc);
                    $fg = 0;
                    $balances[$date] = -$backlog;
                }

                $previousCalc = $calc;
            }

            $item['_balance_dates'] = $balances;
        }
        unset($item);

        foreach ($deliveryData as &$item) {
            $letterIndex = 0;
            foreach ($item['_plan_dates'] as $date => $planQty) {
                $col = numToAlpha($letterIndex + 7);
                $item[$col] = $planQty;
                $letterIndex++;
            }

            $fgCol = numToAlpha($letterIndex + 7);
            $item[$fgCol] = $item['_start_fg'];
            $letterIndex++;

            $negativeCount = 0;
            $counter = 0;

            foreach ($item['_balance_dates'] as $date => $balance) {
                $col = numToAlpha($letterIndex + 7);
                $item[$col] = $balance;
                if ($counter < 5 && $balance < 0) {
                    $negativeCount++;
                }
                $letterIndex++;
                $counter++;
            }

            unset($item['_plan_dates'], $item['_balance_dates'], $item['_start_fg'], $item['_start_backlog']);

            if ($negativeCount > 0) {
                $deliveryDataNegativeBalance[] = $item;
            }
        }
        unset($item);

        $deliveryData = array_values($deliveryData);
    }

    $formattedTimestamp = $latestImportDatetime ? date("Y-m-d h:i:s A", strtotime($latestImportDatetime)) : null;

    echo json_encode([
        'success' => true,
        'firstColDate' => $selectedDate,
        'lastColDate' => $lastPlanDate,
        'latestImportDatetime' => $formattedTimestamp,
        'delivery_plan' => $deliveryData,
        'delivery_plan_negative' => $deliveryDataNegativeBalance,
    ], JSON_THROW_ON_ERROR);

    $conn->close();
    exit;
}
