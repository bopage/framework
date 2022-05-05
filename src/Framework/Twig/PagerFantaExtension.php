<?php

namespace Framework\Twig;

use Framework\Router;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap5View;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PagerFantaExtension extends AbstractExtension
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('paginate', [$this, 'paginate'], ['is_safe' => ['html']])
        ];
    }
    
    /**
     * Génère la pagination
     *
     * @param  Pagerfanta $pagintedResult
     * @param  string $route
     * @param  array $routeParams
     * @param  array $queryAgrs
     * @return string
     */
    public function paginate(
        Pagerfanta $pagintedResult,
        string $route,
        array $routeParams = [],
        array $queryAgrs = []
    ): string {
        $view = new TwitterBootstrap5View();
        return $view->render($pagintedResult, function (int $page) use ($route, $routeParams, $queryAgrs) {
            if ($page > 1) {
                $queryAgrs['p'] = $page;
            }
            return $this->router->generateUri($route, $routeParams, $queryAgrs);
        });
    }
}
