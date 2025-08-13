<?php
require 'vendor/autoload.php';
require_once __DIR__ . '/helpers/php/fillSheetDayLabel.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
date_default_timezone_set(timezoneId: 'Asia/Manila');

$templatePath = __DIR__ . '/uploads/formats/Delivery_Plan_Import_Format.xlsx';

$reader = IOFactory::createReader('Xlsx');
$reader->setLoadSheetsOnly(['DELIVERY PLAN']);
$spreadsheet = $reader->load($templatePath);

$deliverySheet = $spreadsheet->getSheetByName('DELIVERY PLAN');

$now = new DateTime();
$dayLabels = [];
$dateLabels = [];

$start = new DateTime();
$period = new DatePeriod($start, new DateInterval('P1D'), 30);

foreach ($period as $dt) {
    $dayLabels[] = $dt->format('D');
    $dateLabels[] = $dt->format('Y-m-d');
}

$deliverySheet->setCellValue('A1', 'Delivery Plan Import - ' . $now->format('Y.m.d'));

fillSheet(
    $deliverySheet,
    'G',
    $dayLabels,
    $dateLabels
);

$deliverySheet->getStyle('G2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color(Color::COLOR_BLACK));
$deliverySheet->setSelectedCell('A5');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Delivery_Plan_Import_Format.xlsx"');
header('Cache-Control: max-age=0');

IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
exit;
// }
