<?php

use App\Views\TwigRenderer;
use App\Views\JsonRenderer;
use App\Interfaces\RendererInterface;
use Twig\Environment;

function getTwigRenderer(string $uri, Environment $twig): RendererInterface
{
    $path = (string) parse_url($uri, PHP_URL_PATH);

    $isApiRequest = str_starts_with($path, '/count/') || $path === '/users/generate' || $path === '/users/import';

    return $isApiRequest ? new JsonRenderer() : new TwigRenderer($twig);
}
