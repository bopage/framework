<?php

namespace App\Blog\Action;

use App\Blog\Table\CategoryTable;
use Framework\Action\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class CategoryCrudAction extends CrudAction
{

    protected $viewPath = '@blog/admin/categories';

    protected $routePrefix = 'blog.category.admin';

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        CategoryTable $table,
        FlashService $flashService
    ) {
        parent::__construct($renderer, $router, $table, $flashService);
    }

   

    protected function getParams(ServerRequestInterface $request): array
    {
        return  array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 3, 250)
            ->length('slug', 6, 250)
            ->slug('slug');
    }
}
