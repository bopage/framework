<?php
namespace App\Basket;

use App\Basket\Action\BasketAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

class BasketModule extends Module
{
    const DEFINITIONS = __DIR__ . '/definitions.php';

    const NAME = 'basket';

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $router->post('/panier/ajouter/{id:\d+}', BasketAction::class, 'basket.add');
        $router->post('/panier/changer/{id:\d+}', BasketAction::class, 'basket.change');
        $router->get('/panier', BasketAction::class, 'basket');
        $renderer->addPath('basket', __DIR__. '/views');
    }
}
