<?php

use App\Basket\Action\BasketAction;
use App\Basket\Action\Paypal;
use App\Basket\Basket;
use App\Basket\BasketFactory;
use App\Basket\Twig\BasketTwigExtension;

use function DI\add;
use function DI\autowire;
use function DI\factory;
use function DI\get;

return [
'twig.extensions' => add([
    get(BasketTwigExtension::class)
]),
Basket::class => factory(BasketFactory::class),
BasketAction::class => autowire()->constructorParameter('stripeKey', get('stripe.key')),
Paypal::class => autowire()->constructorParameter('paypalKey', get('paypal.key'))
];
