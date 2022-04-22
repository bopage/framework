<?php

namespace Tests\Framework;

use App\Blog\BlogModule;
use Framework\App;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Tests\Framework\Modules\ErrorModule;
use Tests\Framework\Modules\StringModule;

class AppTest extends TestCase
{
    public function testRedirectTrailingSlash()
    {
        $app = new App;

        $request = new ServerRequest('GET', '/demoslash/');
        $reponse = $app->run($request);
        $this->assertContains('/demoslash', $reponse->getHeader('Location'));
        $this->assertEquals(301, $reponse->getStatusCode());
    }

    public function testBlog()
    {
        $app = new App(
            [
                BlogModule::class
            ]
        );
        $request = new ServerRequest('GET', '/blog');
        $response = $app->run($request);
        $this->assertContains('<h1>Bienvenue sur le blog</h1>', [(string)$response->getBody()]);
        $this->assertEquals(200, $response->getStatusCode());

        $requestSimple = new ServerRequest('GET', '/blog/article-de-test');
        $response = $app->run($requestSimple);
        $this->assertContains('<h1>Bienvenue sur l\'article article-de-test</h1>', [(string)$response->getBody()]);
    }

    public function testError()
    {
        $app = new App(
            [
                ErrorModule::class
            ]
        );
        $request = new ServerRequest('GET', '/demo');
        $this->expectException(\Exception::class);
        $app->run($request);
    }

    public function testConvertStringToResponse()
    {
        $app = new App(
            [
                StringModule::class
            ]
        );
        $request = new ServerRequest('GET', '/demo');
        $response = $app->run($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertContains('DEMO', [(string)$response->getBody()]);
    }

    public function testError404()
    {
        $app = new App;
        $request = new ServerRequest('GET', '/aze');
        $response = $app->run($request);
        $this->assertContains('<h1>Error 404</h1>', [(string)$response->getBody()]);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
