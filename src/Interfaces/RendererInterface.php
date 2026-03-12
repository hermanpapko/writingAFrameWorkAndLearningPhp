<?php

namespace App\Interfaces;

interface RendererInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): void;
}
