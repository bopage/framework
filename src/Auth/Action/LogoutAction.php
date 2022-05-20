<?php

namespace App\Auth\Action;

use App\Auth\DatabaseAuth;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;

class LogoutAction
{
    private $renderer;
    private $auth;
    private $flashService;

    public function __construct(
        RendererInterface $renderer,
        DatabaseAuth $auth,
        FlashService $flashService
    ) {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->flashService = $flashService;
    }

    public function __invoke()
    {
        $this->auth->logout();
        $this->flashService->success('Vous êtes maintenant déconnecté');
        return new RedirectResponse('/');
    }
}
