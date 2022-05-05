<?php

namespace Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * TwigRenderer est le moteur de template twig
 * IL permet de simplifier le code par exemple, de ne plus penser à échapper le code pour la récupération
 * en base de donnée et de ne plus faire des issets un peu partoutb
 */
class TwigRenderer implements RendererInterface
{
    private $twig;


    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->twig->getLoader()->addPath($path, $namespace);
    }

    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.twig', $params);
    }

    public function addGlobal(string $key, mixed $value): void
    {
        $this->twig->addGlobal($key, $value);
    }

    /**
     * Get the value of twig
     */
    public function getTwig()
    {
        return $this->twig;
    }
}
