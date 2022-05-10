<?php

namespace Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MethodMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $parsebody = $request->getParsedBody();

        if (array_key_exists('__method', $parsebody) && in_array($parsebody['__method'], ['DELETE', 'PUT'])) {
            $request =  $request->withMethod($parsebody['__method']);
        }
         return $handler->handle($request);
    }
}
