<?php

namespace App\Controllers;

use App\Core\DatabaseWrapper;
use App\Database;
use App\Interfaces\RendererInterface;
use Faker\Factory as FakerFactory;

class UserController
{
    private DatabaseWrapper $db;

    public function __construct(
        private RendererInterface $renderer,
    ) {
        $this->db = Database::getInstance()->getWrapper();
    }

    public function index(): void
    {
        try {
            $rawCountry = $_GET['country'] ?? null;
            $country = $rawCountry ? trim($rawCountry) : null;

            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = 50;
            $offset = ($page - 1) * $perPage;

            $countSql = "SELECT COUNT(*) as total FROM users";
            $sql = "SELECT * FROM users";
            $params = [];
            $whereClause = '';

            if ($country) {
                $whereClause = " WHERE country ILIKE :country";
                $params['country'] = '%' . $country . '%';
            }

            // 1. Count Total
            $countResult = $this->db->fetch($countSql . $whereClause, $params);
            $totalUsers = $countResult['total'] ?? 0;
            $totalPages = ceil($totalUsers / $perPage);

            // 2. Fetch Users
            $sql .= $whereClause . " ORDER BY id ASC LIMIT $perPage OFFSET $offset";
            $users = $this->db->fetchAll($sql, $params);

            $cities = $this->db->fetchAll("SELECT DISTINCT city FROM users ORDER BY city ASC");

            $this->renderer->render('dashboard', [
                'users' => $users,
                'cities' => array_column($cities, 'city'),
                'selected_country' => $country,
                'current_page' => $page,
                'total_pages' => $totalPages
            ]);

        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    public function countByField(string $field): void
    {
        $allowedFields = ['country', 'city', 'is_active', 'gender', 'birth_date', 'salary', 'has_children', 'family_status'];
        if (!in_array($field, $allowedFields)) {
            http_response_code(400);
            $this->renderer->render('error', ['error' => 'Invalid field']);
            return;
        }

        $sql = "SELECT $field as label, COUNT(*) as count FROM users GROUP BY $field";
        $data = $this->db->fetchAll($sql);

        $this->renderer->render('stats', ['stats' => $data]);
    }

    public function generate(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            $this->renderer->render('error', ['error' => 'Method Not Allowed. Use POST.']);
            return;
        }

        $quantityRaw = $_POST['quantity'] ?? null;

        if (!is_scalar($quantityRaw) || !preg_match('/^\d+$/', (string)$quantityRaw)) {
            http_response_code(400);
            $this->renderer->render('error', ['error' => 'Invalid quantity. Must be a positive integer.']);
            return;
        }

        $quantity = (int)$quantityRaw;

        $max = 10000;
        if ($quantity < 1 || $quantity > $max) {
            http_response_code(400);
            $this->renderer->render('error', ['error' => "Quantity must be between 1 and {$max}."]);
            return;
        }

        $filePath = dirname(__DIR__, 2) . '/var/data.txt';

        $dir = dirname($filePath);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            http_response_code(500);
            $this->renderer->render('error', ['error' => 'Failed to create output directory.']);
            return;
        }

        $faker = FakerFactory::create();

        $handle = @fopen($filePath, 'wb');
        if ($handle === false) {
            http_response_code(500);
            $this->renderer->render('error', ['error' => 'Failed to open output file for writing.']);
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
        $this->renderer->render('success', [
            'status' => 'ok',
            'message' => 'Generation complete!',
            'written_lines' => $quantity,
            'file' => 'var/data.txt',
        ]);
    }

    public function import(): void
    {
        $file = $_FILES['user_csv'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            $this->renderer->render('error', ['error' => 'Invalid upload.']);
            return;
        }

        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            http_response_code(413);
            $this->renderer->render('error', ['error' => 'File too large.']);
            return;
        }

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (strtolower($fileExtension) !== 'csv') {
            http_response_code(400);
            $this->renderer->render('error', ['error' => 'Invalid file extension.']);
            return;
        }

        try {
            $parser = new \App\CSVParser();
            $rows = $parser->parse($file['tmp_name']);

            $db = Database::getInstance();
            $repository = new \App\UserRepository($db);

            $count = 0;
            foreach ($rows as $userData) {
                $user = \App\Models\User::hydrate($userData);
                $repository->save($user);
                $count++;
            }

            http_response_code(201);
            $this->renderer->render('success', [
                'status' => 'success',
                'message' => 'Import completed',
                'count' => $count
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            $this->renderer->render('error', ['error' => $e->getMessage()]);
        }
    }

    private function handleError(\Exception $e): void
    {
        http_response_code(500);
        $this->renderer->render('error', ['error' => $e->getMessage()]);
    }
}
