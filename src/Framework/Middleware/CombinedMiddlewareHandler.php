<?php

namespace Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CombinedMiddlewareHandler implements RequestHandlerInterface
{

    private $middlewares = [];

    private $index = 0;

    private $container;

    private $handler;

    public function __construct(
        ContainerInterface $container,
        array $middlewares,
        RequestHandlerInterface $handler
    ) {
        $this->container = $container;
        $this->middlewares = $middlewares;
        $this->handler = $handler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var MiddlewareInterface */
        $middleware = $this->getMiddlware();
        if (is_null($middleware)) {
            return $this->handler->handle($request);
        } elseif (is_callable($middleware)) {
            $response =  call_user_func_array($middleware, [$request, [$this, 'handle']]);
            if (is_string($response)) {
                return new Response(200, [], $response);
            }
            return $response;
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }

    private function getMiddlware(): object
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                $middleware = $this->container->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }
            $this->index++;
            return $middleware;
        }

        return null;
    }
}
