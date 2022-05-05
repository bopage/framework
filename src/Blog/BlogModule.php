<?php

namespace App\Blog;

use App\Blog\Action\BlogAction;
use App\Blog\Action\CategoryCrudAction;
use App\Blog\Action\CategoryShowAction;
use App\Blog\Action\PostCrudAction;
use App\Blog\Action\PostIndexAction;
use App\Blog\Action\PostShowAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

/**
 * BlogModule
 * Génère les routes pour le blog et renvoie les pages correspondantes
 */
class BlogModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';

    const MIGRATIONS  = __DIR__ . '/db/migrations';

    const SEEDS  = __DIR__ . '/db/seeds';

    /**
     * Génère les routes
     *
     * @param  Router $router
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $blogPrefix = $container->get('blog.prefix');
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/views');

        $router = $container->get(Router::class);
        $router->get($blogPrefix, PostIndexAction::class, 'blog.index');
        $router->get($blogPrefix . '/{slug:[a-z\-0-9]+}-{id:[0-9]+}', PostShowAction::class, 'blog.show');
        $router->get($blogPrefix . '/category/{slug:[a-z\-0-9]+}', CategoryShowAction::class, 'blog.category');

        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud("$prefix/posts", PostCrudAction::class, 'blog.admin');
            $router->crud("$prefix/categories", CategoryCrudAction::class, 'blog.category.admin');
        }
    }
}
