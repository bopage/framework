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

    public function __construct(?string $cache = null)
    {
        $this->router = new FastRouteRouter(null, null, [
            FastRouteRouter::CONFIG_CACHE_ENABLED => !is_null($cache),
            FastRouteRouter::CONFIG_CACHE_FILE => $cache
            ]);
    }

    /**
     * Enregistrement des routes en GET
     *
     * @param  string $path
     * @param  callable|string $callback
     * @param  string $name
     * @return void
     */
    public function get(string $path, $callback, ?string $name = null)
    {
        $this->router->addRoute(new RouterRoute($path, new CallableMiddleware($callback), ['GET'], $name));
    }

       /**
     * Enregistrement des routes en POST
     *
     * @param  string $path
     * @param  callable|string $callback
     * @param  string $name
     * @return void
     */
    public function post(string $path, $callback, ?string $name = null)
    {
        $this->router->addRoute(new RouterRoute($path, new CallableMiddleware($callback), ['POST'], $name));
    }

       /**
     * Enregistrement des routes en DELETE
     *
     * @param  string $path
     * @param  callable|string $callback
     * @param  string $name
     * @return void
     */
    public function delete(string $path, $callback, ?string $name = null)
    {
        $this->router->addRoute(new RouterRoute($path, new CallableMiddleware($callback), ['DELETE'], $name));
    }
    
    /**
     * Definition de CRUD
     *
     * @param  string $prefix
     * @param  string $callable
     * @param  string $prefixName
     * @return void
     */
    public function crud(string $prefix, string $callable, string $prefixName)
    {
        $this->get("$prefix", $callable, "$prefixName.index");
        //new
        $this->get("$prefix/new", $callable, "$prefixName.new");
        $this->post("$prefix/new", $callable);
        //edit
        $this->get("$prefix/{id:\d+}", $callable, "$prefixName.edit");
        $this->post("$prefix/{id:\d+}", $callable);
        //delete
        $this->delete("$prefix/{id:\d+}", $callable, "$prefixName.delete");
    }
    
    /**
     * Génère une url
     *
     * @param  string $name
     * @param  array $params
     * @return string|null
     */
    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string
    {
        $uri = $this->router->generateUri($name, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
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
