<?php
namespace App\Basket\Action;

use App\Basket\Basket;
use App\Basket\Table\BasketTable;
use App\shop\Table\ProductTable;
use Framework\API\Stripe;
use Framework\Renderer\RendererInterface;
use Mpociot\VatCalculator\VatCalculator;
use Psr\Http\Message\ServerRequestInterface;

class OrderRecapAction
{
    private $renderer;

    private $basketTable;

    private $stripe;

    private $basket;


    public function __construct(
        RendererInterface $renderer,
        BasketTable $basketTable,
        Stripe $stripe,
        Basket $basket
    ) {
        $this->renderer = $renderer;
        $this->basketTable = $basketTable;
        $this->stripe = $stripe;
        $this->basket = $basket;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $stripeToken = $params['stripeToken'];
        $card = $this->stripe->getCardFromClient($stripeToken);
        $basket = $this->basket;
        $this->basketTable->hydrateBasket($basket);
        $vatCalculator = new VatCalculator();
        $grossPrice = $vatCalculator->calculate($basket->getPrice(), $card->country);
        $taxRate = $vatCalculator->getTaxRate();
        return $this->renderer->render('@basket/recap', compact(
            'basket',
            'taxRate',
            'grossPrice',
            'card',
            'stripeToken'
        ));
    }
}
