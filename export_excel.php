<?php
require 'vendor/autoload.php';
require_once __DIR__ . '/utils/php/generateExcelReport.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Conditional, Alignment, Border, Color};

$input = json_decode(file_get_contents('php://input'), true);
$data = $input['data'] ?? [];
$importDate = new DateTime($input['importDatetime']);
$downloadDate = (new DateTime())->format('Ymd_His');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle($importDate->format('Ymd_His'));

$headers = ['CUSTOMER', 'PART NUMBER', 'ITEM NAME', 'REFERENCE', 'LOCATION', 'BACKLOG'];
$start = new DateTime($importDate->format('Y-m-d'));
$end = (clone $start)->modify('last day of this month');

foreach (new DatePeriod($start, new DateInterval('P1D'), (clone $end)->modify('+1 day')) as $date) {
    $headers[] = $date->format('Y-m-d');
    $planDates[] = $date->format('Y-m-d');
}
$headers[] = 'FG';
foreach (new DatePeriod($start, new DateInterval('P1D'), (clone $end)->modify('+1 day')) as $date) {
    $headers[] = $date->format('Y-m-d');
}

foreach ($headers as $i => $header) {
    $col = Coordinate::stringFromColumnIndex($i + 1);
    $sheet->setCellValue("{$col}3", strtoupper($header));
    $sheet->getStyle("{$col}3")->applyFromArray([
        'font' => ['bold' => true],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['argb' => 'DCE6F1']
        ]

    ]);
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$rowIndex = 4;
foreach ($data as $row) {
    $colIndex = 1;
    $startCol = $endCol = null;
    foreach ($row as $key => $value) {
        if ($key === 'a')
            continue;
        $col = Coordinate::stringFromColumnIndex($colIndex);
        $cell = $sheet->getCell("{$col}{$rowIndex}");

        if ($colIndex >= 6) {
            $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0;(#,##0);"-";@');

            if ($value === '' || $value === '-') {
                $cell->setValue(0);
            } elseif (is_numeric(str_replace(',', '', $value))) {
                $cell->setValue(floatval(str_replace(',', '', $value)));
            }

            $startCol = $startCol ?: $col;
            $endCol = $col;
        } else {
            $cell->setValue($value);
        }
        $colIndex++;
    }

    if ($startCol && $endCol) {
        $fgIndex = array_search('FG', $headers, true) + 1;
        $fgCol = Coordinate::stringFromColumnIndex($fgIndex + 1);

        $range = "{$fgCol}{$rowIndex}:{$endCol}{$rowIndex}";

        $cond = (new Conditional())
            ->setConditionType(Conditional::CONDITION_CELLIS)
            ->setOperatorType(Conditional::OPERATOR_LESSTHAN)
            ->addCondition('0');
        $cond->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCCC');
        $cond->getStyle()->getFont()->setBold(true)
            ->getColor()->setARGB(Color::COLOR_RED);

        $styles = $sheet->getStyle($range)->getConditionalStyles();
        $styles[] = $cond;
        $sheet->getStyle($range)->setConditionalStyles($styles);
    }

    $backlogIndex = array_search('BACKLOG', $headers, true) + 1;
    if ($backlogIndex) {
        $backlogCol = Coordinate::stringFromColumnIndex($backlogIndex);
        $range = "{$backlogCol}{$rowIndex}:{$backlogCol}{$rowIndex}";

        $backlogCond = (new Conditional())
            ->setConditionType(Conditional::CONDITION_CELLIS)
            ->setOperatorType(Conditional::OPERATOR_GREATERTHAN)
            ->addCondition('0');
        $backlogCond->getStyle()->getFont()->setBold(true)
            ->getColor()->setARGB(Color::COLOR_RED);

        $styles = $sheet->getStyle($range)->getConditionalStyles();
        $styles[] = $backlogCond;
        $sheet->getStyle($range)->setConditionalStyles($styles);
    }

    $fgIndex = array_search('FG', $headers, true) + 1;
    $fgCol = Coordinate::stringFromColumnIndex($fgIndex);
    $backlogCol = Coordinate::stringFromColumnIndex(array_search('BACKLOG', $headers, true) + 1);

    $firstBalanceIndex = $fgIndex + 1;
    $firstPlanDateCol = Coordinate::stringFromColumnIndex(array_search($planDates[0], $headers, true) + 1);

    $firstBalanceCol = Coordinate::stringFromColumnIndex($firstBalanceIndex);
    $sheet->setCellValue(
        "{$firstBalanceCol}{$rowIndex}",
        "={$fgCol}{$rowIndex}-{$backlogCol}{$rowIndex}-{$firstPlanDateCol}{$rowIndex}"
    );

    for ($colIndexBal = $firstBalanceIndex + 1; $colIndexBal <= count($headers); $colIndexBal++) {
        $currentBalanceCol = Coordinate::stringFromColumnIndex($colIndexBal);
        $prevBalanceCol = Coordinate::stringFromColumnIndex($colIndexBal - 1);
        $planQtyCol = Coordinate::stringFromColumnIndex($colIndexBal - $fgIndex - 1 + array_search($planDates[0], $headers, true) + 1);

        $sheet->setCellValue(
            "{$currentBalanceCol}{$rowIndex}",
            "={$prevBalanceCol}{$rowIndex}-{$planQtyCol}{$rowIndex}"
        );
    }

    $rowIndex++;
}

$lastRow = $rowIndex - 1;
$lastCol = Coordinate::stringFromColumnIndex(count($headers));
$sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
    'alignment' => ['wrapText' => true],
]);

$highestRow = $sheet->getHighestRow();
$fgPrevColIndex = $fgIndex - 1;
$fgPrevCol = Coordinate::stringFromColumnIndex($fgPrevColIndex);

foreach ([1 => 'With Delivery Issue', 2 => 'Without Delivery Issue'] as $row => $text) {
    $cellMerge = "{$fgPrevCol}{$row}:{$fgCol}{$row}";

    $sheet->mergeCells($cellMerge)
        ->setCellValue("{$fgPrevCol}{$row}", $text);
    $sheet->getStyle($cellMerge)
        ->applyFromArray([
            'font' => [
                'bold' => true,
                "color" => ['argb' => ($row == 1) ? 'FF9C0006' : 'FF006100']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => ($row == 1) ? 'FFFFC7CE' : 'FFC6EFCE']
            ]
        ]);

    $operator = ($text === 'With Delivery Issue') ? '<0' : '>0';

    for ($colIndexBal = $fgIndex + 1; $colIndexBal <= count($headers); $colIndexBal++) {
        $balanceCol = Coordinate::stringFromColumnIndex($colIndexBal);
        $formulaRange = "{$balanceCol}4:{$balanceCol}{$highestRow}";

        $balCoordinate = "{$balanceCol}{$row}";
        $sheet->setCellValue(
            $balCoordinate,
            "=COUNTIF({$formulaRange},\"{$operator}\")"
        );

        $color = ($row === 1) ? Color::COLOR_RED : Color::COLOR_BLACK;

        $sheet->getStyle($balCoordinate)->getFont()
            ->setBold(true)
            ->getColor()->setARGB($color);
    }
}

foreach (range('A', 'E') as $col) {
    $sheet->getStyle($col . '4:' . $col . $sheet->getHighestRow())
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$sheet->setAutoFilter("A3:{$lastCol}3");
$sheet->freezePane('D4');

$excelData = generateExcelReport($spreadsheet);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"{$downloadDate}_Delivery_Plan.xlsx\"");
header('Cache-Control: max-age=0');

echo $excelData;
exit;
