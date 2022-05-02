<?php

namespace App\Blog\Action;

use App\Blog\Table\PostTable;
use Framework\Action\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Framework\Validator;
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

    private $flashService;

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $postTable,
        FlashService $flashService
    ) {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->router = $router;
        $this->flashService = $flashService;
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
            $valid = $this->getValidator($request);
            if ($valid->isValid()) {
                $this->postTable->update($item->id, $params);
                $this->flashService->success('L\'article a bien été modifié');
                return $this->redirect('blog.admin.index');
            }
            $errors = $valid->getErrors();
           $params['id'] =  $item->id;
            $item = $params;
           
        }
        return $this->renderer->render('@blog/admin/edit', compact('item', 'errors'));
    }
    
    /**
     * Création de l'article
     *
     * @param  ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function create(ServerRequestInterface $request)
    {
        $errors = [];
        $item = null;
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params = array_merge($params, [
                'updated_at' => date('Y-m-d H:i'),
                'created_at' => date('Y-m-d H:i')
            ]);
            $valid = $this->getValidator($request);
            if ($valid->isValid()) {
                $this->postTable->insert($params);
                $this->flashService->success('L\'article a bien été crée');
                return $this->redirect('blog.admin.index');
            }
            
            $errors = $valid->getErrors();
            $item = $params;
        }
        return $this->renderer->render('@blog/admin/new', compact('item', 'errors'));
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
        
        $this->flashService->success('L\'article a bien été supprimé');
        return $this->redirect('blog.admin.index');
    }

    private function getParams(ServerRequestInterface $request)
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content']);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function getValidator(ServerRequestInterface $request)
    {
        return (new Validator($request->getParsedBody()))
                ->required('name', 'slug', 'content')
                ->length('name', 3, 250)
                ->length('slug', 6, 250)
                ->length('content', 10)
                ->slug('slug');
    }
}
