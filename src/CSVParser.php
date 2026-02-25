<?php

namespace App;
use Generator;
use Exception;

class CSVParser
{
    public function parse(string $filePath): Generator {
        if (!file_exists($filePath)) {
            throw new Exception("File not found: $filePath");
        }

        $handle = fopen($filePath, 'r');
        fgetcsv($handle, 0, ",", "\"", "\\");

        while (($row = fgetcsv($handle, 0, ",", "\"", "\\")) !== false) {
            yield [
                'country'           => $row[0],
                'city'              => $row[1],
                'is_active'         => filter_var($row[2], FILTER_VALIDATE_BOOLEAN),
                'gender'            => $row[3],
                'birth_date'        => $row[4],
                'salary'            => (float)$row[5],
                'has_children'      => filter_var($row[6], FILTER_VALIDATE_BOOLEAN),
                'family_status'     => $row[7],
                'registration_date' => $row[8]
            ];
        }
        fclose($handle);
    }
}