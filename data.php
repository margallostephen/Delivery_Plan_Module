<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $conn = new mysqli("localhost", "root", "", "1_dps");
    if ($conn->connect_error)
        exit(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));

    $selectedDate = $_POST['selected_date'];

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
        SELECT t1.* 
        FROM delivery_plan_tb t1 
        INNER JOIN ( 
            SELECT PART_NUMBER, PLAN_DATE, MAX(IMPORT_TIME) AS max_import_time 
            FROM delivery_plan_tb
            WHERE IMPORT_DATE = ?
            GROUP BY PART_NUMBER, PLAN_DATE 
        ) t2 ON t1.PART_NUMBER = t2.PART_NUMBER 
            AND t1.PLAN_DATE = t2.PLAN_DATE 
            AND t1.IMPORT_TIME = t2.max_import_time 
        ORDER BY t1.PART_NUMBER DESC;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selectedDate);
    $stmt->execute();
    $result = $stmt->get_result();

    function numToAlpha($index)
    {
        $alpha = '';
        while ($index >= 0) {
            $alpha = chr(97 + ($index % 26)) . $alpha;
            $index = intdiv($index, 26) - 1;
        }
        return $alpha;
    }

    $deliveryData = [];

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
        $deliveryData[$compositeKey]['_plan_dates'][$planDate] = $planQty;
    }

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
            $item[$col] = ($planQty === 0) ? "-" : $planQty;
            $letterIndex++;
        }

        $fgCol = numToAlpha($letterIndex + 7);
        $item[$fgCol] = ($item['_start_fg'] === 0) ? "-" : $item['_start_fg'];
        $letterIndex++;

        foreach ($item['_balance_dates'] as $date => $balance) {
            $col = numToAlpha($letterIndex + 7);
            $item[$col] = ($balance === 0) ? "-" : $balance;
            $letterIndex++;
        }

        unset($item['_plan_dates'], $item['_balance_dates'], $item['_start_fg'], $item['_start_backlog']);
    }
    unset($item);

    $deliveryData = array_values($deliveryData);
    $formattedTimestamp = $latestImportDatetime ? date("Y-m-d h:i:s A", strtotime($latestImportDatetime)) : null;
    
    echo json_encode([
        'success' => true,
        'selectedDate' => $selectedDate,
        'latestImportDatetime' => $formattedTimestamp,
        'delivery_plan' => $deliveryData,
    ], JSON_THROW_ON_ERROR);

    $conn->close();
}
