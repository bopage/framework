<?php

namespace Framework;

use Framework\Router\Route;
use Mezzio\Router\FastRouteRouter;
use Mezzio\Router\Route as RouterRoute;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Router
 * Enregistre les routes et vérifie si elles matchent avec celles obtenues dans la requête
 */
class Router
{
    private $router;

    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }

    /**
     * Enregistrement des routes
     *
     * @param  string $path
     * @param  callable|string $callback
     * @param  string $name
     * @return void
     */
    public function get(string $path, $callback, string $name)
    {
        $this->router->addRoute(new RouterRoute($path, new CallableMiddleware($callback), ['GET'], $name));
    }
    
    /**
     * Génère une url
     *
     * @param  string $name
     * @param  array $params
     * @return string
     */
    public function generateUri(string $name, array $params)
    {
        return $this->router->generateUri($name, $params);
    }

    /**
     * Vérifie s'il ya des correspondances
     *
     * @param  ServerRequestInterface $request
     * @return Route
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request);
        if ($result->isSuccess() === true) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedRoute()->getMiddleware()->getcallable(),
                $result->getMatchedParams()
            );
        }
        return null;
    }
}
