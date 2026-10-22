<?php

namespace Sergi\Misterplan\Service;

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
    }

    public function render(string $template, array $data = []): string
    {
        return $this->twig->render($template, $data);
    }
}
