<?php

namespace App\Blog;

use Framework\Renderer;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface;

/**
 * BlogModule
 * Génère les routes pour le blog et renvoie les pages correspondantes
 */
class BlogModule
{

    /**
     * renderer
     *
     * @var RendererInterface
     */
    private $renderer;
    
    /**
     * Génère les routes
     *
     * @param  Router $router
     * @return void
     */
    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        $this->renderer->addPath('blog', __DIR__ . '/views');

        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/{slug: [a-z\-0-9]+}', [$this, 'show'], 'blog.show');
    }
    
    /**
     * Renvoie la page index du blog
     *
     * @param  ServerRequestInterface $request
     * @return string
     */
    public function index(ServerRequestInterface $request): string
    {
        return $this->renderer->render('@blog/index');
    }
    
    /**
     * Renvoie la vue correspondant à un article
     *
     * @param  ServerRequestInterface $request
     * @return string
     */
    public function show(ServerRequestInterface $request): string
    {
        $response = $request->getAttribute('slug');
        return $this->renderer->render('@blog/show', [
            'slug' => $response
        ]);
    }
}
