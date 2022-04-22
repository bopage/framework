<?php

namespace Tests\Framework;

use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

    
    /**
     * Router
     *
     * @var mixed
     */
    private $router;

    /**
     * @before
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->router = new Router;
    }
    
    /**
     * Vérifie que la route renseignée au niveau de la requête a trouvé une correspondance
     *
     * @return void
     */
    public function testGetMethod()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blog', function () {
            return 'hello';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('hello', call_user_func($route->getCallback(), [$request]));
    }
    
    /**
     * Vérifie que la route return les bons paramètres
     *
     * @return void
     */
    public function testGetMethodWithParameters()
    {

        $request = new ServerRequest('GET', '/blog/mon-slug-3');
        $this->router->get('/blogese', function () {
            return 'hello';
        }, 'blog');
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () {
            return 'hello';
        }, 'post.show');
        $route = $this->router->match($request);
        $this->assertEquals('post.show', $route->getName());
        $this->assertEquals('hello', call_user_func($route->getCallback(), [$request]));
        $this->assertEquals(['slug' => 'mon-slug', 'id' => '3'], $route->getParams());
        // Test invalid url
        $route = $this->router->match(new ServerRequest('GET', '/blog/mon_slug-3'));
        $this->assertEquals(null, $route);
    }
    
    /**
     * Vérifie si la route existe
     *
     * @return void
     */
    public function testGetMethodIfUrlDoesNotExit()
    {

        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blogese', function () {
            return 'hello';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals(null, $route);
    }
    
    /**
     * Génère l'uri
     *
     * @return void
     */
    public function testGenerateUri()
    {
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () {
            return 'hello';
        }, 'post.show');
        $uri = $this->router->generateUri('post.show', ['slug' => 'mon-article', 'id' => '8']);
        $this->assertEquals('/blog/mon-article-8', $uri);
    }
}
