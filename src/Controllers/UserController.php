<?php

namespace App\Controllers;

use App\Database;
use App\UserRepository;
use App\CSVParser;
use Faker\Factory as FakerFactory;
class UserController
{
    public function analyze(): void
    {
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->query("SELECT city FROM public.users");
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($users)) {
                echo "<h1>No users found in database.</h1>";
                echo "<p>Try <a href='/parse'>importing data from CSV</a> first.</p>";
                return;
            }

            echo "<h1>Cities:</h1>";
            echo "<ul>";
            foreach ($users as $user) {
                echo "<li>" . htmlspecialchars($user['city']) . "</li>";
            }
            echo "</ul>";
        } catch (\Exception $e) {
            http_response_code(500);
            echo "Database error: " . $e->getMessage();
        }
    }

    public function parse(): void
    {
        try {
            $db = Database::getInstance();
            $repository = new UserRepository($db);
            $parser = new CSVParser();

            $csvFile = dirname(__DIR__, 2) . '/var/users.csv';
            
            if (!file_exists($csvFile)) {
                http_response_code(404);
                echo "CSV file not found at: $csvFile";
                return;
            }

            $count = 0;
            foreach ($parser->parse($csvFile) as $userData) {
                $repository->save($userData);
                $count++;
            }
            
            echo "Import successful. $count records processed.";
        } catch (\Exception $e) {
            http_response_code(500);
            echo "Parsing error: " . $e->getMessage();
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

        fwrite($handle, "name;email;phone\n");

        for ($i = 0; $i < $quantity; $i++) {
            $line = sprintf(
                "%s;%s;%s\n",
                $faker->name(),
                $faker->unique()->safeEmail(),
                $faker->phoneNumber()
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
}