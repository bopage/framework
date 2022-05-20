<?php

namespace App\Auth\Action;

use Framework\Renderer\RendererInterface;

class LoginAction
{
    private $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke()
    {
        return $this->renderer->render('@auth/login');
    }
}
