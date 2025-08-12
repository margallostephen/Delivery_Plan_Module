<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function generateExcelReport(Spreadsheet $spreadsheet)
{
    ob_start();
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    $excelData = ob_get_clean();

    return $excelData;
}
