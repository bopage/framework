<?php

namespace Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * TwigRenderer est le moteur de template twig
 * IL permet de simplifier le code par exemple, de ne plus penser à échapper le code pour la récupération
 * en base de donnée et de ne plus faire des issets un peu partout
 */
class TwigRenderer implements RendererInterface
{
    private $twig;

    private $loader;

    public function __construct(string $path)
    {
        $this->loader = new FilesystemLoader($path);
        $this->twig = new Environment($this->loader, []);
    }

    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->loader->addPath($path, $namespace);
    }

    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.twig', $params);
    }

    public function addGlobal(string $key, mixed $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}
