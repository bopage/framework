<?php

namespace App\Basket;

use App\Basket\Action\BasketAction;
use App\Basket\Action\OrderInvoiceAction;
use App\Basket\Action\OrderListingAction;
use App\Basket\Action\OrderRecapAction;
use App\Basket\Action\Paypal;
use Framework\Auth\LoggedInMiddleware;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Graphikart\EventManager;

class BasketModule extends Module
{
    const DEFINITIONS = __DIR__ . '/definitions.php';

    const MIGRATIONS = __DIR__ . '/migrations';

    const NAME = 'basket';

    public function __construct(
        Router $router,
        RendererInterface $renderer,
        EventManager $eventManager,
        BasketMerge $basketMerge
    ) {
        $router->post('/panier/ajouter/{id:\d+}', BasketAction::class, 'basket.add');
        $router->post('/panier/changer/{id:\d+}', BasketAction::class, 'basket.change');
        $router->get('/panier', BasketAction::class, 'basket');


        //payment
         //Tunel d'achat
         $router->post(
            '/panier/payment',
            [LoggedInMiddleware::class, Paypal::class],
            'basket.order.payment'
        );

        //Tunel d'achat
        $router->post(
            '/panier/recap',
            [LoggedInMiddleware::class, OrderRecapAction::class],
            'basket.order.recap'
        );
        $router->post(
            '/panier/{id:\d+}',
            [LoggedInMiddleware::class, OrderRecapAction::class],
            'basket.order.process'
        );

        //Gestion des commandes
        $router->get(
            '/mes-commandes',
            [LoggedInMiddleware::class, OrderListingAction::class],
            'basket.orders'
        );
        $router->get(
            '/mes-commandes/{id:\d+}',
            [LoggedInMiddleware::class, OrderInvoiceAction::class],
            'basket.order.invoice'
        );

        $renderer->addPath('basket', __DIR__ . '/views');
        $eventManager->attach('auth.login', $basketMerge);
    }
}
