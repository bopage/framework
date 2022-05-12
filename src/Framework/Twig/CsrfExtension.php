<?php

namespace Framework\Twig;

use Framework\Middleware\CsrfMiddleware;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CsrfExtension extends AbstractExtension
{
    private $csrfMiddleware;

    public function __construct(CsrfMiddleware $csrfMiddleware)
    {
        $this->csrfMiddleware = $csrfMiddleware;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('csrfInput', [$this, 'csrfInput'], ['is_safe' => ['html']])
        ];
    }

    public function csrfInput(): string
    {
        return "<input type='hidden'" .
        "name=" . $this->csrfMiddleware->getFormKey() .
        " value=". $this->csrfMiddleware->generateToken() .
        ">";
    }
}
