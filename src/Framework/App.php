<?php

namespace Framework;

use DI\ContainerBuilder;
use Exception;
use Framework\Middleware\RoutePrefixedMiddleware;
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
    public function pipe(string $routePrefix, ?string $middleware = null): self
    {
        if (is_null($middleware)) {
            $this->middlewares[] = $routePrefix;
        } else {
            $this->middlewares[] = new RoutePrefixedMiddleware($this->getContainer(), $routePrefix, $middleware);
        }

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
    }

    public function getContainer(): ContainerInterface
    {
        $env = null;
        if ($this->container === null) {
            $builder = new ContainerBuilder();
           // $env = getenv('env') ?: 'production';
            if ($env === 'production') {
                $builder->enableCompilation('tmp');
                $builder->writeProxiesToFile(true, 'tmp/proxies');
            }
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
            if (is_string($this->middlewares[$this->index])) {
                $middleware = $this->container->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }
            $this->index++;
            return $middleware;
        }

        return null;
    }

    /**
     * Get liste des modules
     *
     * @return  array
     */
    public function getModules()
    {
        return $this->modules;
    }
}
