<?php

namespace App\Blog\Action;

use App\Blog\Table\PostTable;
use Framework\Action\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface;

class PostShowAction
{

    /**
     * RendererInterface
     *
     * @var RendererInterface
     */
    private $renderer;

    private $pdo;

    private $postTable;

    private $router;

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, Router $router, PostTable $postTable)
    {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
    }

    /**
     * __invoke
     * Permet de render la class callable
     *
     * @param  ServerRequestInterface $request
     * @return void
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $slug = $request->getAttribute('slug');
        $post = $this->postTable->findWithCategory($request->getAttribute('id'));
        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }
        return $this->renderer->render('@blog/show', [
            'post' => $post
        ]);
    }
}
