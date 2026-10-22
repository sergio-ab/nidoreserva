<?php

namespace Sergi\NidoReserva\Service;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TemplateRenderer
{
    private Environment $twig;

    public function __construct()
    {
        $projectRoot = dirname(__DIR__, 2);

        $loader = new FilesystemLoader(
            $projectRoot . '/templates'
        );

        $this->twig = new Environment($loader, [
            'autoescape' => 'html',
            'cache' => false,
            'debug' => false,
        ]);

        $currentPath = parse_url(
            $_SERVER['REQUEST_URI'] ?? '/',
            PHP_URL_PATH
        );

        $this->twig->addGlobal(
            'current_path',
            is_string($currentPath) && $currentPath !== '' ? $currentPath : '/'
        );
    }

    public function render(string $template, array $data = []): string
    {
        return $this->twig->render($template, $data);
    }
}
