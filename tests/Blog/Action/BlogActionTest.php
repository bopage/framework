<?php

namespace Tests\App\Blog\Action;

use App\Blog\Action\BlogAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class BlogActionTest extends TestCase
{
    private $action;

    private $renderer;

    private $pdo;

    private $router;

    protected function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->renderer->render(Argument::any())->willReturn('');
        //Article
        $post = new \stdClass;
        $post->id = 3;
        $post->slug = 'demo-slug';
        //pdo
        $this->pdo = $this->prophesize(PDO::class);
        $pdoStatement = $this->prophesize(PDOStatement::class);
        $this->pdo->prepare(Argument::any())->willReturn($pdoStatement);
        $pdoStatement->execute(Argument::any())->willReturn(true);
        $pdoStatement->fetch()->willReturn($post);

        $this->router = $this->prophesize(Router::class);

        $this->action = new BlogAction(
            $this->renderer->reveal(),
            $this->pdo->reveal(),
            $this->router->reveal()
        );
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
