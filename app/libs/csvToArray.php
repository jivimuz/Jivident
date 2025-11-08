<?php

namespace App\Libraries;

class CsvToArray
{
    public function csvToArray($filename = '', $header = [])
    {
        if (!file_exists($filename) || !is_readable($filename)) return false;

        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($row) === count($header)) {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }
}
