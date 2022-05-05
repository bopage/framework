<?php

namespace Framework\Router;

use Framework\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * RouterTwigExtension
 * Permet de rajouter des functions à twig qui sont liées au routeur
 */
class RouterTwigExtension extends AbstractExtension
{


    /**
     * router
     *
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * getFunction
     *Perment de rajouter la function à twig
     * @return TwigFunction
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('path', [$this, 'pathFor']),
            new TwigFunction('is_subPath', [$this, 'isSubPath'])
        ];
    }

    public function pathFor(string $name, array $params = []): string
    {
        return $this->router->generateUri($name, $params);
    }

    public function isSubPath(string $path): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $expectedUri = $this->router->generateUri($path);
        return strpos($uri, $expectedUri) !== false;
    }
}
