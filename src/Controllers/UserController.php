<?php

namespace App\Controllers;

use App\Database;
use App\UserRepository;
use App\CSVParser;
use Faker\Factory as FakerFactory;
use PDO;

class UserController
{
    public function index(): void
    {
        try {
            $db = Database::getInstance()->getWrapper();
            $cities = $db->fetchAll("SELECT DISTINCT city FROM users");
            $cities = array_column($cities, 'city');

            include __DIR__ . "/../Views/index.php";

        } catch (\Exception $e) {
            http_response_code(500);
            echo "Database error: " . $e->getMessage();
        }
    }

    public function generate(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Method Not Allowed. Use POST.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $quantityRaw = $_POST['quantity'] ?? null;

        if (!is_scalar($quantityRaw) || !preg_match('/^\d+$/', (string)$quantityRaw)) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Invalid quantity. Must be a positive integer.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $quantity = (int)$quantityRaw;

        $max = 10000;
        if ($quantity < 1 || $quantity > $max) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => "Quantity must be between 1 and {$max}."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $filePath = dirname(__DIR__, 2) . '/var/data.txt';

        $dir = dirname($filePath);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Failed to create output directory.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $faker = FakerFactory::create();

        $handle = @fopen($filePath, 'wb');
        if ($handle === false) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Failed to open output file for writing.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        fwrite($handle, "country;city;is_active;gender;birth_date;salary;has_children;family_status;registration_date\n");

        for ($i = 0; $i < $quantity; $i++) {
            $line = sprintf(
                "%s;%s;%d;%s;%s;%.2f;%d;%s;%s\n",
                $faker->country(),
                $faker->city(),
                $faker->boolean() ? 1 : 0,
                $faker->randomElement(['male', 'female']),
                $faker->date(),
                $faker->randomFloat(2, 20000, 150000),
                $faker->boolean() ? 1 : 0,
                $faker->randomElement(['single', 'married', 'divorced']),
                $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d H:i:s')
            );
            fwrite($handle, $line);
        }

        fclose($handle);

        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            [
                'status' => 'ok',
                'written_lines' => $quantity,
                'file' => 'var/data.txt',
            ],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function import(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $file = $_FILES['user_csv'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid upload.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            http_response_code(413);
            echo json_encode(['error' => 'File too large.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (strtolower($fileExtension) !== 'csv') {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file extension.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $parser = new CSVParser();
            $rows = $parser->parse($file['tmp_name']);

            $db = Database::getInstance();
            $repository = new UserRepository($db);

            $count = 0;
            foreach ($rows as $userData) {
                $user = \App\Models\User::hydrate($userData);
                $repository->save($user);
                $count++;
            }

            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Import completed',
                'count' => $count
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}
