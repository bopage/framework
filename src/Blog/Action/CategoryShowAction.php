<?php

namespace App\Blog\Action;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Action\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class CategoryShowAction
{
    private $renderer;

    private $pdo;

    private $postTable;

    private $categoryTable;

    use RouterAwareAction;

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
        $category = $this->categoryTable->findBy('slug', $request->getAttribute('slug'));
        $posts = $this->postTable->findPublicForcategory($category->id)->paginate(12, $params['p'] ?? 1);
        $categories = $this->categoryTable->findAll();
        $page = $params['p'] ?? 1;
        return $this->renderer->render('@blog/index', compact('posts', 'categories', 'category', 'page'));
    }
}
