<?php

namespace App\Blog\Action;

use App\Blog\Table\PostTable;
use Framework\Action\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface;

class AdminBlogAction
{

    /**
     * RendererInterface
     *
     * @var RendererInterface
     */
    private $renderer;

    private $pdo;

    private $router;

    private $postTable;

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, Router $router, PostTable $postTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->router = $router;
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
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }

        if (substr((string)($request->getUri()), -3) === 'new') {
            return $this->create($request);
        }

        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    /**
     * Renvoie la page index du blog
     *
     * @return string
     */
    public function index(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->postTable->findPagineted(12, $params['p'] ?? 1);
        return $this->renderer->render('@blog/admin/index', compact('items'));
    }
    
    /**
     * Edition de l'article
     *
     * @param  ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function edit(ServerRequestInterface $request)
    {
        $item = $this->postTable->find($request->getAttribute('id'));
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params['updated_at'] = date('Y-m-d H:i');
            $this->postTable->update($item->id, $params);
            return $this->redirect('blog.admin.index');
        }
        return $this->renderer->render('@blog/admin/edit', compact('item'));
    }
    
    /**
     * Création de l'article
     *
     * @param  ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function create(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params = array_merge($params, [
                'updated_at' => date('Y-m-d H:i'),
                'created_at' => date('Y-m-d H:i')
            ]);
            $this->postTable->insert($params);
            return $this->redirect('blog.admin.index');
        }
        return $this->renderer->render('@blog/admin/new');
    }
    
    /**
     * Suppression de l'article
     *
     * @param  ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function delete(ServerRequestInterface $request)
    {
        $this->postTable->delete($request->getAttribute('id'));
        
        return $this->redirect('blog.admin.index');
    }

    private function getParams(ServerRequestInterface $request)
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content']);
        }, ARRAY_FILTER_USE_KEY);
    }
}
