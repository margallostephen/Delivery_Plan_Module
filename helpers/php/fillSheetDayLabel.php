<?php

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

function fillDateLabels($sheet, $startColLetter, $dayLabels, $dateLabels)
{
    $startColIndex = Coordinate::columnIndexFromString($startColLetter);

    for ($i = 0; $i < count($dayLabels); $i++) {
        $colIndex = $startColIndex + $i;
        $colLetter = Coordinate::stringFromColumnIndex($colIndex);

        $sheet->getStyle("{$colLetter}3")
            ->getNumberFormat()
            ->setFormatCode('mm-dd');
        $sheet->setCellValue("{$colLetter}3", $dateLabels[$i]);

        $sheet->setCellValue("{$colLetter}4", $dayLabels[$i]);
    }
}
