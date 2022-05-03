<?php

namespace Framework\Action;

use Framework\Action\RouterAwareAction;
use Framework\Database\Table;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class CrudAction
{

    /**
     * RendererInterface
     *
     * @var RendererInterface
     */
    private $renderer;

    private $pdo;

    private $router;

    private $Table;

    private $flashService;


    /**
     * viewPath
     *
     * @var string
     */
    protected $viewPath;

    /**
     * routePrefix
     *
     * @var string
     */
    protected $routePrefix;

    protected $message = [
        'create' => 'L\'élément a bien été crée',
        'edit' => 'L\'élément a bien été modifié',
        'delete' => 'L\'élément a bien été supprimé'
    ];

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        Table $Table,
        FlashService $flashService
    ) {
        $this->renderer = $renderer;
        $this->Table = $Table;
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
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
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
     * Renvoie la liste des éléments
     *
     * @return string
     */
    public function index(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->Table->findPagineted(12, $params['p'] ?? 1);
        return $this->renderer->render($this->viewPath . '/index', compact('items'));
    }

    /**
     * Edition de l'élément
     *
     * @param  ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function edit(ServerRequestInterface $request)
    {
        $errors = null;
        $item = $this->Table->find($request->getAttribute('id'));
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $valid = $this->getValidator($request);
            if ($valid->isValid()) {
                $this->Table->update($item->id, $params);
                $this->flashService->success($this->message['edit']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $errors = $valid->getErrors();
            $params['id'] =  $item->id;
            $item = $params;
        }
        return $this->renderer->render(
            $this->viewPath . '/edit',
            $this->fromParams(compact('item', 'errors'))
        );
    }

    /**
     * Création de l'élément
     *
     * @param  ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function create(ServerRequestInterface $request)
    {
        $errors = [];
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $valid = $this->getValidator($request);
            if ($valid->isValid()) {
                $this->Table->insert($params);
                $this->flashService->success($this->message['create']);
                return $this->redirect($this->routePrefix . '.index');
            }

            $errors = $valid->getErrors();
            $item = $params;
        }
        return $this->renderer->render(
            $this->viewPath . '/new',
            $this->fromParams(compact('item', 'errors'))
        );
    }

    /**
     * Suppression de l'élément
     *
     * @param  ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function delete(ServerRequestInterface $request)
    {
        $this->Table->delete($request->getAttribute('id'));

        $this->flashService->success($this->message['delete']);
        return $this->redirect($this->routePrefix . '.index');
    }

    /**
     * Renvoie les paramètres filtré
     *
     * @param  mixed $request
     * @return array
     */
    protected function getParams(ServerRequestInterface $request): array
    {
        return  array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, []);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Validation des données
     *
     * @param  mixed $request
     * @return Validator
     */
    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return new Validator($request->getParsedBody());
    }

    /**
     * Permet de définir une nouvelle entitée
     *
     * @return void
     */
    protected function getNewEntity()
    {
        return [];
    }

    /**
     * Permet de traiter les paramètre à la vue
     *
     * @param  mixed $params
     * @return array
     */
    protected function fromParams(array $params): array
    {
        return $params;
    }
}
