#!/usr/bin/env php
<?php

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require_once '../vendor/autoload.php';

$args = $_SERVER['argv'];

$inputFileName = $args[1];

if(!file_exists($inputFileName)) {
    echo "Error: File not found.\n";
    exit(1);
}

$spreadsheet = (new Reader)->load($inputFileName);

$sheetIndex = (count($args) > 3 ? $args[3] : 1) - 1;

$datos = $spreadsheet->getSheet($sheetIndex)->toArray();

$chunks = [];

$chunkSize = $args[2] ?: 1000;

$chunks = array_chunk($datos, $chunkSize);

$divideInSheets = count($args) > 4 ? !!$args[4] : false;

$inputFileName = str_replace('.xlsx',  '', $inputFileName);

foreach($chunks as $index => $chunk) {

    if($divideInSheets) {
        $hoja = $spreadsheet->createSheet();
        $hoja->fromArray($chunk);
    }
    else {
        $excel = new Spreadsheet();
        $hoja = $excel->getSheet(0);
        $hoja->fromArray($chunk);
        $writer = new Writer($excel);
        $writer->save("{$inputFileName}_{$index}.xlsx");
    }
}

if($divideInSheets) {
    $writer = new Writer($spreadsheet);
    $writer->save($inputFileName.".xlsx");
}
