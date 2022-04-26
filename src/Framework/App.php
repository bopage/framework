<?php

namespace Framework;

use Exception;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{
    /**
     * liste des modules
     *
     * @var array
     */
    private $modules = [];


    /**
     * router
     *
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, array $modules = [],)
    {
        $this->container = $container;
        foreach ($modules as $module) {
            $this->modules[] = $this->container->get($module);
        }
    }

    public function run(ServerRequestInterface $request): Response
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === '/') {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }
        $router = $this->container->get(Router::class);
        $route = $router->match($request);
        if (is_null($route)) {
            return new Response(404, [], '<h1>Error 404</h1>');
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }
        $reponse = call_user_func_array($callback, [$request]);
        if (is_string($reponse)) {
            return new Response(200, [], $reponse);
        } elseif ($reponse instanceof Response) {
            return $reponse;
        } else {
            throw new Exception('La reponse n\'est une chaîne de caractère ou une instance de Response');
        }
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
