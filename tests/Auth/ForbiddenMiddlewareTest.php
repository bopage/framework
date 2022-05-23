<?php

namespace Tests\App\Auth;

use framework\Session\SessionInterface;
use App\Auth\ForbiddenMiddleware;
use Framework\Auth\ForbiddenException;
use Framework\Session\ArraySession;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TypeError;

class ForbiddenMiddlewareTest extends TestCase
{

    
    /**
     * session
     *
     * @var SessionInterface
     */
    private $session;

    protected function setUp(): void
    {
        $this->session = new ArraySession;
    }

    public function makeRequest($path = '/')
    {
        $uri = $this->getMockBuilder(UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn($path);
        $request =  $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $request->method('getUri')->willReturn($uri);
        return $request;
    }

    public function makeHandler()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handler->method('handle')->willReturn($this->getMockBuilder(ResponseInterface::class)->getMock());
        return $handler;
    }

    public function makeMiddleware()
    {
        return new ForbiddenMiddleware('/login', $this->session);
    }

    public function testCatchForbiddenException()
    {
        $handler = $this->makeHandler();
        $handler->expects($this->once())->method('handle')->willThrowException(new ForbiddenException());
        $response = $this->makeMiddleware()->process($this->makeRequest('/tests'), $handler);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/login'], $response->getHeader('Location'));
        $this->assertEquals('/tests', $this->session->get('auth.redirect'));
    }
}
