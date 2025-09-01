<?php
ini_set('max_execution_time', 300);
set_time_limit(300);
require 'vendor/autoload.php';
require_once __DIR__ . '/utils/php/generateExcelReport.php';
require_once __DIR__ . '/utils/php/fetchDataPhpScript.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Conditional, Alignment, Border, Color};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sendEmail = false;
    $input = json_decode(file_get_contents('php://input'), true);
    $data = $input['data'] ?? [];

    if (empty($data)) {
        $sendEmail = true;
        $input = fetchDataFromDataPhp('data');
        $data = $input['delivery_plan'];
        $importDate = new DateTime($input['latestImportDatetime']);
    } else {
        $importDate = new DateTime($input['importDatetime']);
    }

    $downloadDate = (new DateTime())->format('Ymd_His');

    $spreadsheet = new Spreadsheet();
    $spreadsheet->getDefaultStyle()->getFont()
        ->setName('Tahoma')
        ->setSize(10);

    $sheet = $spreadsheet->getActiveSheet();
    $sheet->getDefaultRowDimension()->setRowHeight(15);
    $sheet->setTitle($importDate->format('Ymd_His'));

    $headers = ['GROUP', 'CUSTOMER', 'PART NUMBER', 'ITEM NAME', 'REFERENCE', 'LOCATION', 'BACKLOG'];
    $staticHeaderCount = count($headers);
    $planDays = [];
    $rowDataIndex = 5;
    $start = new DateTime($importDate->format('Y-m-d'));
    $end = (clone $start)->modify('+31 days');

    for ($dt = clone $start; $dt < $end; $dt->modify('+1 day')) {
        $md = $dt->format('m-d');
        $headers[] = $md;
        $planDates[] = $md;
        $planDays[] = $dt->format('D');
    }

    $headers[] = 'FG';

    for ($dt = clone $start; $dt < $end; $dt->modify('+1 day')) {
        $headers[] = $dt->format('m-d');
    }

    $headerStyle = [
        'font'      => [
            'bold' => true
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
            'wrapText'   => true
        ],
        'fill'      => [
            'fillType' => Fill::FILL_SOLID,
            'color'    => ['argb' => 'DCE6F1']
        ]
    ];

    $fgIndex = array_search('FG', $headers, true) + 1;
    $backlogIndex = array_search('BACKLOG', $headers, true) + 1;
    $firstPlanIndex = array_search($planDates[0], $headers, true) + 1;
    $firstBalIndex = $fgIndex + 1;
    $lastBalIndex = $firstBalIndex + count($planDates) - 1;
    $lastPlanIndex = $fgIndex - 1;
    $dayIndex = 0;

    $fgCol = Coordinate::stringFromColumnIndex($fgIndex);
    $backlogCol = Coordinate::stringFromColumnIndex($backlogIndex);
    $firstPlanCol = Coordinate::stringFromColumnIndex($firstPlanIndex);
    $firstBalCol = Coordinate::stringFromColumnIndex($firstBalIndex);
    $lastPlanCol = Coordinate::stringFromColumnIndex($lastPlanIndex);

    foreach ($headers as $i => $header) {
        $col = Coordinate::stringFromColumnIndex($i + 1);
        $headerCell = "{$col}3";
        $cellDay = "{$col}4";

        $sheet->setCellValue($headerCell, strtoupper($header));

        if ($i < $staticHeaderCount || $dayIndex == 31) {
            $sheet->mergeCells("{$headerCell}:{$cellDay}");
            $dayIndex = 0;
        } else {
            $sheet->setCellValue($cellDay, $planDays[$dayIndex]);
            $sheet->getStyle($cellDay)->applyFromArray($headerStyle);
            $dayIndex++;
        }

        $sheet->getStyle($headerCell)->applyFromArray($headerStyle);
    }

    $rowIndex = $rowDataIndex;
    foreach ($data as $row) {
        $colIndex = 1;
        $startCol = $endCol = null;
        foreach ($row as $key => $value) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $cell = $sheet->getCell("{$col}{$rowIndex}");

            if ($colIndex >= $staticHeaderCount) {
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

            $cell->getStyle()
                ->getAlignment()
                ->setHorizontal($colIndex < $staticHeaderCount ? Alignment::HORIZONTAL_LEFT : Alignment::HORIZONTAL_RIGHT)
                ->setVertical(Alignment::VERTICAL_CENTER);

            if ($colIndex != 4) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            } else {
                $sheet->getColumnDimension($col)->setWidth(70);
            }

            $colIndex++;
        }

        if ($startCol && $endCol) {
            $range = "{$firstBalCol}{$rowIndex}:{$endCol}{$rowIndex}";

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

        $sheet->setCellValue(
            "{$firstBalCol}{$rowIndex}",
            "={$fgCol}{$rowIndex}-{$backlogCol}{$rowIndex}-{$firstPlanCol}{$rowIndex}"
        );

        for ($currBalIndex = $firstBalIndex + 1; $currBalIndex <= count($headers); $currBalIndex++) {
            $currentBalanceCol = Coordinate::stringFromColumnIndex($currBalIndex);
            $prevBalanceCol = Coordinate::stringFromColumnIndex($currBalIndex - 1);
            $planQtyCol = Coordinate::stringFromColumnIndex($currBalIndex - $fgIndex - 1 + $firstPlanIndex);

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

    foreach ([1 => 'With Delivery Issue', 2 => 'Without Delivery Issue'] as $row => $text) {
        $cellMerge = "{$lastPlanCol}{$row}:{$fgCol}{$row}";

        $operator = ($text === 'With Delivery Issue') ? '<0' : '>0';

        for ($currBalIndex = $fgIndex + 1; $currBalIndex <= count($headers); $currBalIndex++) {
            $currBalanceCol = Coordinate::stringFromColumnIndex($currBalIndex);
            $formulaRange = "{$currBalanceCol}{$rowDataIndex}:{$currBalanceCol}{$highestRow}";

            $balCoordinate = "{$currBalanceCol}{$row}";

            $formula = "=SUMPRODUCT(SUBTOTAL(103,OFFSET({$currBalanceCol}{$rowDataIndex},ROW({$currBalanceCol}{$rowDataIndex}:{$currBalanceCol}{$highestRow})-ROW({$currBalanceCol}{$rowDataIndex}),0)),--({$currBalanceCol}{$rowDataIndex}:{$currBalanceCol}{$highestRow}{$operator}))";

            $sheet->setCellValue($balCoordinate, $formula);

            $color = ($row === 1) ? Color::COLOR_RED : Color::COLOR_BLACK;
            $sheet->getStyle($balCoordinate)->getFont()
                ->setBold(true)
                ->getColor()->setARGB($color);
        }
    }

    foreach ([[$firstPlanIndex, $lastPlanIndex], [$firstBalIndex, $lastBalIndex]] as [$start, $end]) {
        for ($i = $start + 5; $i <= $end; $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))
                ->setOutlineLevel(1)
                ->setVisible(false);
        }
    }

    $sheet->calculateColumnWidths();
    $sheet->setAutoFilter("A4:{$lastCol}4");
    $sheet->freezePane("E{$rowDataIndex}");
    $sheet->setSelectedCell("A3");

    $excelData = generateExcelReport($spreadsheet);

    if ($sendEmail) {
        echo $excelData;
    } else {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$downloadDate}_Delivery_Plan.xlsx\"");
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
    }
    exit;
}
