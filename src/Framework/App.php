<?php

namespace Framework;

use DI\ContainerBuilder;
use Exception;
use Framework\Middleware\CombinedMiddleware;
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
     * @var array
     */
    private $definitions;

    private $middlewares = [];

    private $index = 0;

    public function __construct($definitions = [])
    {
        if (is_string($definitions)) {
            $this->definitions = [$definitions];
        }
        if (!$this->isSequential($definitions)) {
            $this->definitions = [$definitions];
        }
        $this->definitions = $definitions;
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
     * @param  string|callable|MiddlewareInterface $routePrefix
     * @param  string|callable|MiddlewareInterface $middleware
     * @return self
     */
    public function pipe($routePrefix, $middleware = null): self
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
        $this->index++;
        if ($this->index > 1) {
            throw new Exception();
        }
        $middleware = new CombinedMiddleware($this->getContainer(), $this->middlewares);
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
            foreach ($this->definitions as $definition) {
                $builder->addDefinitions($definition);
            }
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $builder->addDefinitions([
                App::class => $this
            ]);
            $this->container = $builder->build();
        }
        return $this->container;
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

    private function isSequential(array $array): bool
    {
        if (empty($array)) {
            return true;
        }

        return array_keys($array) === range(0, count($array) - 1);
    }
}
