<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

date_default_timezone_set(timezoneId: 'Asia/Manila');

$templatePath = __DIR__ . '/uploads/formats/Delivery_Plan_Import_Format.xlsx';

$reader = IOFactory::createReader('Xlsx');
$reader->setLoadSheetsOnly(['DELIVERY PLAN']);
$spreadsheet = $reader->load($templatePath);

$deliverySheet = $spreadsheet->getSheetByName('DELIVERY PLAN');

$now = new DateTime();
$dayLabels = [];
$dateLabels = [];

$start = new DateTime($now->format('Y-m-01'));
$end = new DateTime($now->format('Y-m-t'));

for ($dt = clone $start; $dt <= $end; $dt->modify('+1 day')) {
    $dayLabels[] = $dt->format('D');
    $dateLabels[] = $dt->format('Y-m-d');
}

function fillSheet($sheet, $startColLetter, $dayLabels, $dateLabels)
{

    $startColIndex = Coordinate::columnIndexFromString($startColLetter);

    for ($i = 0; $i < count($dayLabels); $i++) {
        $colIndex = $startColIndex + $i;
        $colLetter = Coordinate::stringFromColumnIndex($colIndex);

        $sheet->setCellValue("{$colLetter}3", $dayLabels[$i]);
        $sheet->setCellValue("{$colLetter}4", $dateLabels[$i]);
    }
}

$deliverySheet->setCellValue('A1', 'Delivery Plan Import - ' . $now->format('Y.m.d'));

fillSheet(
    $deliverySheet,
    'G',
    $dayLabels,
    $dateLabels
);

$daysToHide = (int) date('j') - 1;

$startColumnIndex = Coordinate::columnIndexFromString('G');

for ($i = 0; $i < $daysToHide; $i++) {
    $colLetter = Coordinate::stringFromColumnIndex($startColumnIndex + $i);
    $deliverySheet->getColumnDimension($colLetter)->setVisible(false);
}

if ((int) $end->format('j') === 31) {
    $deliverySheet->getColumnDimension('AX')->setVisible(false);
}

$deliverySheet->getStyle('G2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color(Color::COLOR_BLACK));
$deliverySheet->setSelectedCell('A5');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Delivery_Plan_Import_Format.xlsx"');
header('Cache-Control: max-age=0');

IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
exit;
