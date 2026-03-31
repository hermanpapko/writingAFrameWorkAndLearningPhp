<?php

namespace App\Controllers;

use App\Core\DatabaseWrapper;
use App\Interfaces\RendererInterface;
use App\Database;

class AuthController
{
    private DatabaseWrapper $db;

    public function __construct(private RendererInterface $renderer)
    {
        $this->db = Database::getInstance()->getWrapper();
    }

    public function showLogin(): void
    {
        $this->renderer->render('auth/login', ['error' => null]);
    }

    public function login(): void
    {
        $email = $_POST["email"] ?? '';
        $password = $_POST["password"] ?? '';

        $user = $this->db->fetch("SELECT * FROM system_users WHERE email = :email", ['email' => $email]);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];

            header("Location: /organizations");
            exit;
        }

        $this->renderer->render('auth/login', ['error' => 'Invalid email or password']);
    }

    public function logout(): void
    {
        session_destroy();
        header("Location: /login");
        exit;
    }
}
