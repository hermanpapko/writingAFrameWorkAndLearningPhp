<?php
require_once __DIR__ . '/../vendor/autoload.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$controller = new App\Controllers\UserController();
try {
    if ($requestUri === '/analyze') {
        $controller->analyze();
    } elseif ($requestUri === '/parse') {
        $controller->parse();
    } elseif ($requestUri === '/generate') {
        $controller->generate();
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <title>Управление приложением</title>
            <style>
                body { font-family: sans-serif; max-width: 600px; margin: 50px auto; line-height: 1.6; }
                .card { border: 1px solid #ddd; padding: 20px; border-radius: 8px; background: #f9f9f9; }
                input[type="number"] { padding: 8px; width: 100px; }
                button { padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
                button:hover { background: #0056b3; }
                ul { list-style: none; padding: 0; }
                li { margin-bottom: 10px; }
                a { color: #007bff; text-decoration: none; }
            </style>
        </head>
        <body>
        <h1>Панель управления</h1>

        <div class="card">
            <h3>Генерация данных (POST)</h3>
            <form action="/generate" method="POST">
                <p>
                    <label for="qty">Количество записей:</label><br>
                    <input type="number" id="qty" name="quantity" value="10" min="1" max="10000">
                    <button type="submit">Сгенерировать файл</button>
                </p>
            </form>
        </div>

        <h3>Доступные отчеты:</h3>
        <ul>
            <li><a href="/analyze">Просмотреть анализ данных (/analyze)</a></li>
            <li><a href="/parse">Запустить парсинг файла (/parse)</a></li>
        </ul>
        </body>
        </html>
        <?php
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}