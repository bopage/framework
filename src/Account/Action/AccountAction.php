<?php

namespace App\Account\Action;

use Framework\Auth;
use Framework\Renderer\RendererInterface;

class AccountAction
{
    private $renderer;
    private $auth;

    public function __construct(
        RendererInterface $renderer,
        Auth $auth
    ) {
        $this->renderer = $renderer;
        $this->auth = $auth;
    }

    public function __invoke()
    {
        $user = $this->auth->getUser();
        return $this->renderer->render('@account/account', compact('user'));
    }
}
