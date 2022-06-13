<?php

use App\Account\AccountModule;
use App\Admin\AdminModule;
use App\Auth\AuthModule;
use App\Auth\ForbiddenMiddleware;
use App\Basket\BasketModule;
use App\Blog\Action\PostIndexAction;
use App\Blog\BlogModule;
use App\Contact\ContactModule;
use App\Shop\ShopModule;
use Framework\Middleware\DispatcherMiddleware;
use Framework\Middleware\NotFoundMiddleware;
use Framework\App;
use Framework\Auth\RoleMiddlewareFactory;
use Framework\Middleware\CsrfMiddleware;
use Framework\Middleware\MethodMiddleware;
use Framework\Middleware\RendererRequestMiddleware;
use Framework\Middleware\RouterMiddleware;
use Framework\Middleware\TrailingSlashMiddleware;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;

use function Http\Response\send;

chdir(dirname(__DIR__));

require 'vendor/autoload.php';


$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();


$app = (new App(['config/config.php', 'config.php']))
->addModule(AdminModule::class)
->addModule(ContactModule::class)
->addModule(ShopModule::class)
->addModule(BlogModule::class)
->addModule(AuthModule::class)
->addModule(AccountModule::class)
->addModule(BasketModule::class);

$container = $app->getContainer();
$container->get(Router::class)->get('/', PostIndexAction::class, 'home');
$app->pipe(TrailingSlashMiddleware::class)
    ->pipe(ForbiddenMiddleware::class)
    ->pipe(
        $container->get('admin.prefix'),
        $container->get(RoleMiddlewareFactory::class)->makeForRole('admin')
    )
    ->pipe(MethodMiddleware::class)
    ->pipe(RendererRequestMiddleware::class)
    ->pipe(CsrfMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class);

if (php_sapi_name() !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());
    send($response);
}
