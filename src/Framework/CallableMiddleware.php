<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * CallableMiddleware
 * Récupère la function
 */
class CallableMiddleware implements MiddlewareInterface
{

    
    /**
     * Le traitement de la function
     *
     * @var mixed
     */
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    
    /**
     * Return la function
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return $this->callable;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new Response();
    }
}
