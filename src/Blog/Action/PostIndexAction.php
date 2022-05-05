<?php

namespace App\Blog\Action;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostIndexAction
{

    /**
     * RendererInterface
     *
     * @var RendererInterface
     */
    private $renderer;

    private $pdo;

    private $postTable;

    private $categoryTable;


    public function __construct(
        RendererInterface $renderer,
        PostTable $postTable,
        CategoryTable $categoryTable
    ) {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->categoryTable = $categoryTable;
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
        $params = $request->getQueryParams();
        $posts = $this->postTable->findPaginetedPublic(12, $params['p'] ?? 1);
        $categories = $this->categoryTable->findAll();
        return $this->renderer->render('@blog/index', compact('posts', 'categories'));
    }
}
