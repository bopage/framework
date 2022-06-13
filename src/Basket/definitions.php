<?php

use App\Basket\Basket;
use App\Basket\SessionBasket;
use App\Basket\Twig\BasketTwigExtension;

use function DI\add;
use function DI\create;
use function DI\decorate;
use function DI\factory;
use function DI\get;

return [
'twig.extensions' => add([
    get(BasketTwigExtension::class)
]),
Basket::class => get(SessionBasket::class)
];
