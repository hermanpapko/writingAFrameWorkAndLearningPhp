

# Writing a Framework and Learning PHP

Учебный проект по созданию собственного PHP-фреймворка с нуля. Включает в себя реализацию базового роутинга, работу с базой данных (PostgreSQL), генерацию данных и Docker-окружение.

## Технологии

- **PHP 8.4**
- **PostgreSQL 17**
- **Nginx**
- **Docker & Docker Compose**
- **Composer** (для автозагрузки и dev-зависимостей)
- **Faker** (для генерации случайных данных)
- **PHPStan** & **PHP-CS-Fixer** (для контроля качества кода)

## Структура проекта

- `core/` — ядро фреймворка (роутинг и базовая логика).
- `src/` — исходный код приложения (контроллеры, модели, репозитории).
- `public/` — точка входа (`index.php`) и публичные ассеты.
- `config/` — конфигурационные файлы для инструментов (PHPStan, CS-Fixer).
- `docker/` — конфигурации Docker (PHP, Nginx).
- `var/` — временные файлы и сгенерированные данные.
- `db/` — SQL-скрипты для инициализации БД.

## Установка и запуск

### 1. Клонирование репозитория
```bash
git clone <repository_url>
cd writingAFrameWorkAndLearningPhp
```

### 2. Настройка окружения
Создайте файл `.env` (если его нет) на основе примера:
```env
DB_NAME=course_db
DB_USER=postgres
DB_PASSWORD=123
DB_HOST=db
DB_PORT=5432
```

### 3. Запуск через Docker
```bash
docker-compose up -d --build
```
После запуска проект будет доступен по адресу: [http://localhost:8080](http://localhost:8080)

### 4. Установка зависимостей
```bash
docker-compose exec php composer install
```

## Использование

### Генерация данных
Для генерации большого объема тестовых данных в CSV формате:
```bash
docker-compose exec php php csvgen.php
```
Файл сохранится в `var/users.csv`.

### Инструменты разработки
Проект поддерживает автоматическую проверку и исправление кода:

- **Запуск статического анализа (PHPStan):**
  ```bash
  docker-compose exec php composer run stan
  ```
- **Автоматическое исправление стиля кода (PHP-CS-Fixer):**
  ```bash
  docker-compose exec php composer run fix
  ```

## Роутинг
Маршруты описываются в `public/index.php`. На данный момент реализованы:
- `GET /` — главная страница.
- `POST /users/generate` — генерация данных через Faker.
- `POST /users/import` — импорт пользователей из загруженного CSV-файла.
