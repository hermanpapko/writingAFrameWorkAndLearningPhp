<?php


$targetRows = 60000;
$fileName = __DIR__ . '/var/users.csv';

$countries = ['USA', 'France', 'Germany', 'UK', 'Canada', 'Japan', 'Spain', 'Italy'];
$cities = ['New York', 'Paris', 'Berlin', 'London', 'Toronto', 'Tokyo', 'Madrid', 'Rome'];
$genders = ['male', 'female', 'non-binary'];
$familyStatuses = ['single', 'married', 'divorced', 'widowed'];

if (!is_dir(dirname($fileName))) {
    mkdir(dirname($fileName), 0777, true);
}

$handle = fopen($fileName, 'w');

fputcsv($handle, [
    'country', 'city', 'isActive', 'gender', 'birthDate',
    'salary', 'hasChildren', 'familyStatus', 'registrationDate'
]);

echo "Starting generation of $targetRows rows...\n";

for ($i = 0; $i < $targetRows; $i++) {
    $row = [
        $countries[array_rand($countries)],
        $cities[array_rand($cities)],
        (rand(0, 1) ? 'true' : 'false'),
        $genders[array_rand($genders)],
        date('Y-m-d', strtotime('-' . rand(18, 65) . ' years')),
        number_format(rand(200000, 900000) / 100, 2, '.', ''),
        (rand(0, 1) ? 'true' : 'false'),
        $familyStatuses[array_rand($familyStatuses)],
        date('Y-m-d H:i:s', strtotime('-' . rand(0, 365) . ' days'))
    ];

    fputcsv($handle, $row);

    if ($i % 100000 === 0) {
        echo "Processed $i rows...\n";
    }
}

fclose($handle);
echo "Done! File saved to: $fileName\n";
