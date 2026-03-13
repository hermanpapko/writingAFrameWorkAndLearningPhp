# PHP Mini-Framework

This is a lightweight educational PHP framework designed to demonstrate key web development concepts such as routing, an MVC-like architecture, dependency injection, and database interaction.

## Key Features

- **Controller-Based Architecture**: Application logic is organized into controller classes.
- **Flexible Routing**: A simple yet powerful router that supports both exact and regex-based paths.
- **Dependency Injection (DI)**: Dependencies (like the renderer) are injected into controllers via their constructors.
- **Abstract Rendering System**: Allows for easy switching between HTML output (via Twig) and JSON.
- **Database Wrapper**: Simplifies database interaction using a PDO wrapper.
- **`.env` Configuration**: All environment settings are managed in an `.env` file.
- **Docker Environment**: A pre-configured setup for local development using Nginx, PHP, and PostgreSQL.
- **Static Analysis**: Integrated with PHPStan to maintain code quality.

## Getting Started

### Requirements

- Docker and Docker Compose
- Composer

### Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/hermanpapko/writingAFrameWorkAndLearningPhp
    cd writingAFrameWorkAndLearningPhp
    ```

2.  **Create an `.env` file** from the example and fill it out:
    ```bash
    cp .env .env
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

5.  **Open the application in your browser:**
    Navigate to [http://localhost:8080](http://localhost:8080).

## Project Structure

```
.
├── config/             # Static analyzer configuration
├── docker/             # Docker configuration files (Nginx, PHP)
├── public/             # Web root, entry point (index.php)
├── src/                # Application source code
│   ├── Controllers/    # Controller classes
│   ├── Core/           # Framework core (router, DB wrapper)
│   ├── Interfaces/     # Interfaces (e.g., RendererInterface)
│   ├── Views/          # Rendering classes (Twig, JSON)
│   └── ...
├── templates/          # Twig HTML templates
├── var/                # Temporary files, generated data
├── vendor/             # Composer dependencies
├── .env                # Environment variables file
├── docker-compose.yml  # Docker Compose configuration
└── README.md           # This file
```

## Core Concepts

### 1. Routing

The router (`src/Core/Routing/Router.php`) is responsible for mapping a request URI to a controller method. Routes are defined in `public/index.php`.

-   **Exact Path**:
    ```php
    $router->get('/', [$userController, 'index']);
    ```
-   **Regex-Based Path**:
    ```php
    $router->get('/count/(\w+)', [$userController, 'countByField']);
    ```

### 2. Controllers

Controllers (`src/Controllers/`) contain the main application logic. Dependencies, such as the renderer, are injected through the constructor.

```php
// src/Controllers/UserController.php

class UserController
{
    private RendererInterface $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function index(): void
    {
        // ... logic ...
        $this->renderer->render('dashboard', ['users' => $users]);
    }
}
```

### 3. Rendering (Views)

The framework uses the `RendererInterface` to abstract output, allowing for flexible response formats.

-   `TwigRenderer`: Renders HTML templates using Twig.
-   `JsonRenderer`: Serializes data into JSON format.

The choice of renderer is determined in `public/index.php` based on the request URI.

### 4. Database Interaction

The `DatabaseWrapper` class provides convenient methods for working with the database, abstracting away the boilerplate of using PDO. The database connection is a singleton configured via `.env`.

## Available Endpoints

-   `GET /`: Displays the main dashboard with a user table and statistics.
-   `POST /users/generate`: Generates a specified number of users and saves them to `var/data.txt`.
-   `POST /users/import`: Imports users from an uploaded CSV file into the database.
-   `GET /count/{field}`: An API endpoint that returns grouped statistics for a specified field (`country`, `gender`, etc.) in JSON format.

## Development Tools

-   **Static Analysis**: PHPStan is used to check code quality.
    ```bash
    composer stan
    ```
-   **Debugging**: The Docker environment is configured with Xdebug for step-debugging.
```