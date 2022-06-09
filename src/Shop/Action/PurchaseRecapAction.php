<?php
namespace App\Shop\Action;

use App\shop\Table\ProductTable;
use Framework\API\Stripe;
use Framework\Renderer\RendererInterface;
use Mpociot\VatCalculator\VatCalculator;
use Psr\Http\Message\ServerRequestInterface;

class PurchaseRecapAction
{
    private $renderer;

    private $productTable;

    private $stripe;

    private $stripeToken;

    public function __construct(
        RendererInterface $renderer,
        ProductTable $productTable,
        Stripe $stripe,
        string $stripeToken
    ) {
        $this->renderer = $renderer;
        $this->productTable = $productTable;
        $this->stripe = $stripe;
        $this->stripeToken = $stripeToken;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        //$stripeToken = $params['stripeToken'];
        $stripeToken = $this->stripeToken;
        $card = $this->stripe->getCardFromClient($stripeToken);
        $product = $this->productTable->find((int)$request->getAttribute('id'));
        $vatCalculator = new VatCalculator();
        $grossPrice = $vatCalculator->calculate($product->getPrice(), $card->country);
        $taxRate = $vatCalculator->getTaxRate();
        return $this->renderer->render('@shop/show', compact(
            'product',
            'taxRate',
            'grossPrice',
            'card',
            'stripeToken'
        ));
    }
}
