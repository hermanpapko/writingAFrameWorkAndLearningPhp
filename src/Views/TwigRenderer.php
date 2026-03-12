<?php

namespace App\Views;

use App\Interfaces\RendererInterface;
use Twig\Environment;

class TwigRenderer implements RendererInterface
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): void
    {
        echo $this->twig->render($template . '.html.twig', $data);
    }
}
