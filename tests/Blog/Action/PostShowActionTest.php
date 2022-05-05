<?php

namespace Tests\App\Blog\Action;

use App\Blog\Action\PostShowAction;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class PostShowActionTest extends TestCase
{
    private $router;

    private $renderer;

    private $postTable;


    protected function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->router = $this->prophesize(Router::class);
       

        $this->postTable = $this->prophesize(PostTable::class);

        $this->action = new PostShowAction(
            $this->renderer->reveal(),
            $this->postTable->reveal(),
            $this->router->revea
        );
    }

    public function makePost(int $id, string $slug)
    {
        //Article
        $post = new Post;
        $post->id = $id;
        $post->slug = $slug;

        return $post;
    }

    public function testShowRedirect()
    {
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', 9)
            ->withAttribute('slug', 'demo');
        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(301, $response->getStatusCode());
    }
}
