<?php

use App\Shop\Action\PurchaseRecapAction;
use App\Shop\ShopWidget;
use Framework\API\Stripe;

use function DI\add;
use function DI\autowire;
use function DI\create;
use function DI\get;

return [
    'admin.widgets' => add([
        get(ShopWidget::class)
    ]),
    Stripe::class => create()->constructor(get('stripe.secret')),
    PurchaseRecapAction::class => autowire()->constructorParameter('stripeToken', get('stripe.key'))
];
