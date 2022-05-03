<?php

namespace App\Blog\Action;

use App\Blog\Entity\Post;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use DateTime;
use Framework\Action\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class PostCrudAction extends CrudAction
{

    protected $viewPath = '@blog/admin/posts';

    protected $routePrefix = 'blog.admin';

    private $categoryTable;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flashService,
        CategoryTable $categoryTable
    ) {
        parent::__construct($renderer, $router, $table, $flashService);
        $this->categoryTable = $categoryTable;
    }

    protected function getNewEntity()
    {
        $post = new Post;
        $post->created_at = new DateTime();

        return $post;
    }

    protected function fromParams(array $params): array
    {
         $params['categories'] = $this->categoryTable->findList();
         $params['categories']['231546'] = 'fake category';
         return $params;
    }

    protected function getParams(ServerRequestInterface $request): array
    {
        $params =  array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id']);
        }, ARRAY_FILTER_USE_KEY);

        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i')
        ]);
    }

    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return parent::getValidator($request)
            ->required('name', 'slug', 'content', 'created_at', 'category_id')
            ->length('name', 3, 250)
            ->length('slug', 6, 250)
            ->length('content', 10)
            ->datetime('created_at')
            ->exist('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
            ->slug('slug');
    }
}
