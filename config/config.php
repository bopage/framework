<?php

use Framework\Middleware\CsrfMiddleware;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;
use Framework\Router\RouterFactory;
use Framework\Router\RouterTwigExtension;
use Framework\Session\PHPSession;
use Framework\Session\SessionInterface;
use Framework\Twig\CsrfExtension;
use Framework\Twig\DebugExtension;
use Framework\Twig\FlashServiceExtension;
use Framework\Twig\FormExtension;
use Framework\Twig\PagerFantaExtension;
use Framework\Twig\TextExtension;
use Framework\Twig\TimeExtension;
use Psr\Container\ContainerInterface;

use function DI\create;
use function DI\factory;
use function DI\get;

return [
    'env' => env('ENV', 'dev'),
    'database.host' => 'localhost',
    'database.name' => 'monsupersite',
    'database.username' => 'root',
    'database.password' => 'root',
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        get(RouterTwigExtension::class),
        get(TextExtension::class),
        get(TimeExtension::class),
        get(DebugExtension::class),
        get(FlashServiceExtension::class),
        get(FormExtension::class),
        get(CsrfExtension::class),
        get(PagerFantaExtension::class)
    ],
    SessionInterface::class => get(PHPSession::class),
    CsrfMiddleware::class => create()->constructor(get(SessionInterface::class)),
    Router::class => factory(RouterFactory::class),
    RendererInterface::class => factory(TwigRendererFactory::class),
    PDO::class => function (ContainerInterface $c) {
        return  new PDO(
            "mysql:host=" . $c->get('database.host') .
                ";dbname=" . $c->get('database.name'),
            $c->get('database.username'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
];
