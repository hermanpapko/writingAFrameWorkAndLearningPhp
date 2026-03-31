# PHP Mini-Framework

This is a lightweight educational PHP framework designed to demonstrate key web development concepts such as routing, an MVC-like architecture, dependency injection, database interaction, custom migrations, and authentication.

## Key Features

- **Controller-Based Architecture**: Application logic is organized into specific controller classes (e.g., `UserController`, `OrganizationController`, `AuthController`).
- **Flexible Routing**: A simple yet powerful router that supports both exact and regex-based paths, configured in a dedicated routes file.
- **Dependency Injection (DI)**: Dependencies (like the renderer) are injected into controllers via their constructors.
- **Abstract Rendering System**: Allows for easy switching between HTML output (via Twig) and JSON responses for API endpoints.
- **Database Wrapper**: Simplifies database interaction using a PDO wrapper with a singleton connection.
- **Custom Migration System**: A built-in CLI tool (`bin/console.php`) to track and apply SQL migrations automatically.
- **`.env` Configuration**: All environment settings are managed in an `.env` file using `vlucas/phpdotenv`.
- **Docker Environment**: A pre-configured setup for local development using Nginx, PHP 8.4, and PostgreSQL 17.
- **Code Quality Tools**: Integrated with PHPStan for static analysis and PHP-CS-Fixer for code formatting.

## Getting Started

### Requirements

- Docker and Docker Compose
- Composer

### Installation

1.  **Clone the repository:**
    ```bash
    git clone [https://github.com/hermanpapko/writingAFrameWorkAndLearningPhp](https://github.com/hermanpapko/writingAFrameWorkAndLearningPhp)
    cd writingAFrameWorkAndLearningPhp
    ```

2.  **Create an `.env` file** from the example and fill it out:
    ```bash
    cp .env.example .env # Or create manually if no example file exists
    ```
    The contents of `.env` should look like this:
    ```dotenv
    DB_HOST=db
    DB_PORT=5432
    DB_NAME=your_db_name
    DB_USER=your_db_user
    DB_PASSWORD=your_db_password
    ```

3.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

4.  **Start the Docker containers:**
    ```bash
    docker-compose up -d --build
    ```

5.  **Run Database Migrations:**
    Execute the custom console script inside the PHP container to set up your database schema (users, organizations, auth tables, etc.):
    ```bash
    docker-compose exec php php bin/console.php
    ```

6.  **Open the application in your browser:**
    Navigate to [http://localhost:8080](http://localhost:8080).

## Project Structure

```text
.
├── bin/                # CLI scripts (e.g., console.php for migrations)
├── config/             # Static analyzer and linter configuration
├── docker/             # Docker configuration files (Nginx, PHP)
├── migrations/         # SQL migration files
├── public/             # Web root, entry point (index.php)
├── src/                # Application source code
│   ├── Controllers/    # Controller classes (User, Organization, Auth)
│   ├── Core/           # Framework core (Router, DB wrapper, bootstrap)
│   ├── Interfaces/     # Interfaces (e.g., RendererInterface)
│   ├── Models/         # Data models
│   └── Views/          # Rendering classes (TwigRenderer, JsonRenderer)
├── templates/          # Twig HTML templates (auth, organizations, etc.)
├── var/                # Temporary files, generated data (e.g., users.csv)
├── vendor/             # Composer dependencies
├── .env                # Environment variables file
├── docker-compose.yml  # Docker Compose configuration
└── README.md           # This file