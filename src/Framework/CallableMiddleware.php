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
     * @var callable|string
     */
    private $callable;

    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    
    /**
     * Return la function
     *
     * @return callable|string
     */
    public function getCallable()
    {
        return $this->callable;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new Response();
    }
}
