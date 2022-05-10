<?php
namespace Framework\Middleware;

use Exception;
use Framework\Router\Route;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DispatcherMiddleware implements MiddlewareInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute(Route::class);
        if (is_null($route)) {
            return $handler->handle($request);
        }
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
}
