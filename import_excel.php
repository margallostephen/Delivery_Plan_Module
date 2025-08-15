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

        $dateHeaders = $rows[2];
        $dataRows = array_slice($rows, 4);

        $dataRows = array_filter($dataRows, function ($row) {
            foreach ($row as $cell) {
                if (trim((string)$cell) !== '') {
                    return true;
                }
            }
            return false;
        });

        $insertSql = "INSERT INTO delivery_plan_tb 
            (CUSTOMER, PART_NUMBER, ITEM_NAME, REFERENCE, LOCATION, BACKLOG, PLAN_DATE, PLAN_QTY, FG, IMPORT_DATE_TIME)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
            exit;
        }

        foreach ($dataRows as $row) {
            if (empty($row[1]) || empty($row[2]))
                continue;

            $customer = strtoupper(trim($row[0]));
            $partNo = strtoupper(trim($row[1]));
            $itemName = strtoupper(trim($row[2]));
            $reference = preg_replace('/\s+/', "\n", trim($row[3]));
            $location = strtoupper(trim($row[4]));
            $backlog = (int) str_replace(['(', ')', ',', ' '], '', trim($row[5]));
            $fg = (int) str_replace(['(', ')', ',', ' '], '', trim($row[37]));

            for ($col = 6; $col <= 36; $col++) {
                $planDate = trim($dateHeaders[$col]);

                $rawQty = trim($row[$col]);
                $cleanQty = str_replace(['(', ')', ',', ' '], '', $rawQty);
                $qty = (int) $cleanQty;

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
