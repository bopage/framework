<?php

namespace App\Blog\Action;

use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class BlogAction
{
    
    /**
     * RendererInterface
     *
     * @var RendererInterface
     */
    private $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
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
        if ($slug) {
            return $this->show($slug);
        }
        return $this->index();
    }

    /**
     * Renvoie la page index du blog
     *
     * @return string
     */
    public function index(): string
    {
        return $this->renderer->render('@blog/index');
    }
    
    /**
     * Renvoie la vue correspondant à un article
     *
     * @param  string $slug
     * @return string
     */
    public function show(string $slug): string
    {
        return $this->renderer->render('@blog/show', [
            'slug' => $slug
        ]);
    }
}
