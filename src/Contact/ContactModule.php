<?php

namespace App\Contact;

use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use SebastianBergmann\CodeCoverage\Report\Html\Renderer;

class ContactModule extends Module
{
    const DEFINITIONS = __DIR__ . '/definitions.php';

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('contact', __DIR__ . '/views');
        $router->get('/conctact', ContactAction::class, 'contact');
        $router->post('/conctact', ContactAction::class);
    }
}
