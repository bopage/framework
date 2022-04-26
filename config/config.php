<?php

use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;
use Framework\Router\RouterTwigExtension;

use function DI\create;
use function DI\factory;
use function DI\get;

return [
    'database.host' => 'localhost',
    'database.name' => 'monsupersite',
    'database.username' => 'root',
    'database.password' => 'root',
    'views.path' => dirname(__DIR__). '/views',
    'twig.extensions' => [
        get(RouterTwigExtension::class)
    ],
    Router::class => create(),
    RendererInterface::class => factory(TwigRendererFactory::class)
];