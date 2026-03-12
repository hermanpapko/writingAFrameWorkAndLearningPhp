<?php

namespace App\Views;

use App\Interfaces\RendererInterface;

class JsonRenderer implements RendererInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
