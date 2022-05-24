<?php

namespace App\Account;

use App\Account\Action\SignUpAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

class AccountModule extends Module
{
    public function __construct(Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('account', __DIR__ . '/views');
        $router->get('/account', SignUpAction::class, 'account.signup');
        $router->post('/account', SignUpAction::class);
        $router->get('/mon-profil', SignUpAction::class, 'account.profil');
    }
}
