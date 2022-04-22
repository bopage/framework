<?php

namespace App\Blog;

use Framework\Router;
use Psr\Http\Message\ServerRequestInterface;

/**
 * BlogModule
 * Génère les routes pour le blog et renvoie les pages correspondantes
 */
class BlogModule
{

    
    /**
     * Génère les routes
     *
     * @param  Router $router
     * @return void
     */
    public function __construct(Router $router)
    {
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/{slug: [a-z\-]+}', [$this, 'show'], 'blog.show');
    }
    
    /**
     * Renvoie la page index du blog
     *
     * @param  ServerRequestInterface $request
     * @return string
     */
    public function index(ServerRequestInterface $request): string
    {
        return '<h1>Bienvenue sur le blog</h1>';
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
        return '<h1>Bienvenue sur l\'article ' . $response . '</h1>';
    }
}
