<?php

declare(strict_types=1);

namespace App\Controllers;

class LanguageController
{
    public function switchLanguage(string $lang): void
    {
        if (in_array($lang, ['en', 'ru'])) {
            setcookie('lang', $lang, [
              'expires' => time() + (86400 * 30),
              'path' => '/',
              'samesite' => 'Lax'
            ]);
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: $referer");
        exit;
    }
}
