<?php

namespace App;

use App\Interfaces\ParserInterface;
use Generator;
use Exception;

class CSVParser implements ParserInterface
{
    /**
     * @param string $filePath
     * @param string $separator
     * @return Generator<int, array{
     *     country: string,
     *     city: string,
     *     is_active: bool,
     *     gender: string,
     *     birth_date: string,
     *     salary: float,
     *     has_children: bool,
     *     family_status: string,
     *     registration_date: string
     * }>
     */
    public function parse(string $filePath, string $separator = ','): Generator
    {
        if (!file_exists($filePath)) {
            throw new Exception("File not found: $filePath");
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception("Could not open file: $filePath");
        }

        // Попытка автоопределения разделителя (запятая или точка с запятой)
        $header = fgets($handle);
        if ($header !== false) {
            $commas = substr_count($header, ',');
            $semicolons = substr_count($header, ';');
            $separator = ($semicolons > $commas) ? ';' : ',';
            rewind($handle);
        }

        // Пропускаем строку заголовков
        fgetcsv($handle, 0, $separator, '"', "\\");

        while (($row = fgetcsv($handle, 0, $separator, '"', "\\")) !== false) {
            if (count($row) < 9) {
                continue;
            }
            yield [
                'country'           => (string)$row[0],
                'city'              => (string)$row[1],
                'is_active'         => filter_var($row[2], FILTER_VALIDATE_BOOLEAN),
                'gender'            => (string)$row[3],
                'birth_date'        => (string)$row[4],
                'salary'            => (float)$row[5],
                'has_children'      => filter_var($row[6], FILTER_VALIDATE_BOOLEAN),
                'family_status'     => (string)$row[7],
                'registration_date' => (string)$row[8]
            ];
        }
        fclose($handle);
    }
}
