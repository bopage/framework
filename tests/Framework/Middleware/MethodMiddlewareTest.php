<?php

namespace Tests\Framework\Middleware\MethodMiddleware;

use Framework\Middleware\MethodMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Whoops\Handler\HandlerInterface;

class MethodMiddlewareTest extends TestCase
{


    /**
     * middleware
     *
     * @var MethodMiddleware
     */
    private $middleware;

    protected function setUp(): void
    {
        $this->middleware = new MethodMiddleware;
    }

    public function testAddMethod()
    {
        $handle = $this->getMockBuilder(HandlerInterface::class)
            ->onlyMethods(['handle'])
            ->getMock();
        $handle->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($request) {
                return $request->getMethod() === 'DELETE';
            }));
        $request = (new ServerRequest('POST', '/demo'))
            ->withParsedBody(['_method' => 'DELETE']);
        $this->middleware->process($request, $handle);
    }
}
