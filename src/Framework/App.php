<?php

namespace Framework;

use DI\ContainerBuilder;
use Exception;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class App implements RequestHandlerInterface
{
    /**
     * liste des modules
     *
     * @var array
     */
    private $modules = [];


    /**
     * router
     *
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * Le dossier de configuration
     *
     * @var string
     */
    private $definition;

    private $middlewares = [];

    private $index = 0;

    public function __construct(string $definition)
    {
        $this->definition = $definition;
    }
    
    /**
     * RAjoute les différents modules, un module est une fonctionalité
     *
     * @param  string $modules
     * @return self
     */
    public function addModule(string $modules): self
    {
        $this->modules[] = $modules;

        return $this;
    }
    
    /**
     * Rajoute les différents middlewares, un middleware est un comportement au niveau de la requête
     *
     * @param  string $middleware
     * @return self
     */
    public function pipe(string $middleware): self
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var MiddlewareInterface */
        $middleware = $this->getMiddlware();
        if (is_null($middleware)) {
            throw new Exception('Aucun middleware n\ a intercepté cette requête');
        }
        return $middleware->process($request, $this);
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }

        return $this->handle($request);
        /*
        $uri = $request->getUri()->getPath();
        $parsebody = $request->getParsedBody();

        if (array_key_exists('__method', $parsebody) && in_array($parsebody['__method'], ['DELETE', 'PUT'])) {
            $request =  $request->withMethod($parsebody['__method']);
        }

        if (!empty($uri) && $uri[-1] === '/') {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }
        $router = $this->container->get(Router::class);
        $route = $router->match($request);
        if (is_null($route)) {
            return new Response(404, [], '<h1>Error 404</h1>');
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }
        $reponse = call_user_func_array($callback, [$request]);
        if (is_string($reponse)) {
            return new Response(200, [], $reponse);
        } elseif ($reponse instanceof Response) {
            return $reponse;
        } else {
            throw new Exception('La reponse n\'est une chaîne de caractère ou une instance de Response');
        }
        */
    }

    private function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $builder->addDefinitions($this->definition);
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }

            $this->container = $builder->build();
        }
        return $this->container;
    }

    private function getMiddlware(): object
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->container->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        }

        return null;
    }
}
