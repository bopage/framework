<?php
namespace App\Basket\Twig;

use App\Basket\Basket;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BasketTwigExtension extends AbstractExtension
{
    private $basket;

    public function __construct(Basket $basket)
    {
        $this->basket = $basket;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('basket_count', [$this->basket, 'count'])
        ];
    }
}
