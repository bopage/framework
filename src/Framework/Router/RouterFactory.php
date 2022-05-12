<?php

namespace Framework\Router;

use Framework\Router;
use Psr\Container\ContainerInterface;

class RouterFactory
{
    public function __invoke(ContainerInterface $containerInterface)
    {
        $cache = null;
        if ($containerInterface->get('env') === 'production') {
            $cache = 'tmp/routes';
        }
        return new Router($cache);
    }
}
