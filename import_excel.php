<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$conn = new mysqli("localhost", "root", "", "1_dps");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    try {
        $uploadDir = __DIR__ . '/uploads/excel_imports/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = basename($_FILES['file']['name']);
        $timestamp = date('Ymd_His');
        $savedFilePath = $uploadDir . $timestamp . "_" . $originalName;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $savedFilePath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save the uploaded file.']);
            exit;
        }

        $spreadsheet = IOFactory::load($savedFilePath);
        $sheet = $spreadsheet->getActiveSheet();

        $import_date = date('Y-m-d H:i:s');

        $rows = $sheet->toArray();
        $dateHeaders = $rows[3]; // row 2, index 1 — contains delivery plan dates
        $dataRows = array_slice($rows, 4); // start from row 5 onward

        // Define static column indexes
        $table = 'delivery_plan_tb';
        $colMap = [
            'CUSTOMER' => 0,
            'PART_NUMBER' => 1,
            'ITEM_NAME' => 2,
            'REFERENCE' => 3,
            'LOCATION' => 4,
            'BACKLOG' => 5,
        ];

        // Prepare insert once
        $insertSql = "INSERT INTO $table (CUSTOMER, PART_NUMBER, ITEM_NAME, REFERENCE, LOCATION, BACKLOG, PLAN_DATE, PLAN_QTY, FG, IMPORT_DATE_TIME)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
            exit;
        }

        foreach ($dataRows as $row) {
            if (empty($row[1]) || empty($row[2]))
                continue; // skip if part number or item name missing

            // Static fields
            $customer = strtoupper(trim($row[$colMap['CUSTOMER']]));
            $partNo = strtoupper(trim($row[$colMap['PART_NUMBER']]));
            $itemName = strtoupper(trim($row[$colMap['ITEM_NAME']]));
            $reference = preg_replace('/\s+/', "\n", trim($row[$colMap['REFERENCE']]));
            $location = strtoupper(trim($row[$colMap['LOCATION']]));
            $backlog = (int) str_replace(['(', ')', ',', ' '], '', trim($row[$colMap['BACKLOG']]));
            $fg = (int) str_replace(['(', ')', ',', ' '], '', trim($row[37])); // AL column is index 37

            // Loop through plan dates from G (index 6) to AL (index 36)

            $dayPlusFive = (int) date('j') + 5;

            for ($col = $dayPlusFive; $col <= 36; $col++) {
                $planDate = trim($dateHeaders[$col]);
                if (!$planDate || !strtotime($planDate))
                    continue;

                $rawQty = trim($row[$col]); // e.g., " (1,000) "
                $cleanQty = str_replace(['(', ')', ',', ' '], '', $rawQty);
                $qty = (int) $cleanQty;
                // if ($qty === 0)
                //     continue; // skip if no planned qty

                $stmt->bind_param(
                    'sssssssiis',
                    $customer,
                    $partNo,
                    $itemName,
                    $reference,
                    $location,
                    $backlog,
                    $planDate,
                    $qty,
                    $fg,
                    $import_date
                );

                $stmt->execute();
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'File imported and data inserted per plan date.',
            'file_path' => $savedFilePath
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
