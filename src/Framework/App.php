<?php

namespace Framework;

use Exception;
use GuzzleHttp\Psr7\Response;
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
     * @var Router
     */
    private $router;

    public function __construct(array $modules = [], $dependancies = [])
    {
        $this->router = new Router;
        if ($dependancies['renderer']) {
            $dependancies['renderer']->addGlobal('router', $this->router);
        }
        foreach ($modules as $module) {
            $this->modules[] = new $module($this->router, $dependancies['renderer']);
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
        $route = $this->router->match($request);
        if (is_null($route)) {
            return new Response(404, [], '<h1>Error 404</h1>');
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $reponse = call_user_func($route->getCallback(), $request);
        if (is_string($reponse)) {
            return new Response(200, [], $reponse);
        } elseif ($reponse instanceof Response) {
            return $reponse;
        } else {
            throw new Exception('La reponse n\'est une chaîne de caractère ou une instance de Response');
        }
    }
}
