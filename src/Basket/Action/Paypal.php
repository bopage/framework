<?php

namespace App\Basket\Action;

use App\Basket\Basket;
use App\Basket\PurchaseBasket;
use App\Basket\Table\BasketTable;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Mpociot\VatCalculator\VatCalculator;
use Psr\Http\Message\ServerRequestInterface;

class Paypal
{
    private $basketTable;
    private $auth;
    private $flashService;
    private $basket;
    private $paypalKey;
    private $renderer;

    public function __construct(
        BasketTable $basketTable,
        Auth $auth,
        FlashService $flashService,
        Basket $basket,
        string $paypalKey,
        RendererInterface $renderer
    ) {
        $this->basketTable = $basketTable;
        $this->auth = $auth;
        $this->flashService = $flashService;
        $this->basket = $basket;
        $this->paypalKey = $paypalKey;
        $this->renderer = $renderer;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $basket = $this->basket;
        $this->basketTable->hydrateBasket($basket);
        $vatCalculator = new VatCalculator();
        $grossPrice = $vatCalculator->calculate($basket->getPrice());
        $order = json_encode([
            'purchase_units' => [[
                'description' => 'panier tutoriel',
                'items' => array_map(function ($product) {
                    return [
                        'name' => $product->getProduct()->getName(),
                        'quantity' => $product->getQuantity(),
                        'unit_amount' => [
                            'value' => $product->getProduct()->getPrice(),
                            'currency_code' => 'EUR'
                        ]
                    ];
                }, $basket->getRows()),
                'amount' => [
                    'currency_code' => 'EUR',
                    'value' => $grossPrice,
                    'breakdown' => [
                        'item_total' => [
                            'currency_code' => 'EUR',
                            'value' => $grossPrice
                        ]
                    ]
                ]
            ]]
        ]);

        $html = <<<HTML
             <!-- Replace "test" with your own sandbox Business account app client ID -->
        <script src="https://www.paypal.com/sdk/js?client-id={$this->paypalKey}&currency=EUR"></script>
        <!-- Set up a container element for the button -->
        <div id="paypal-button-container"></div>
        <script>
            paypal.Buttons({
                // Sets up the transaction when a payment button is clicked
                createOrder: (data, actions) => {
                return actions.order.create({$order});
                },
                // Finalize the transaction after payer approval
                onApprove: (data, actions) => {
                return actions.order.capture().then(function(orderData) {
                    // Successful capture! For dev/demo purposes:
                    console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                    const transaction = orderData.purchase_units[0].payments.captures[0];
                    alert(`Transaction \${transaction . status}: \${transaction . id}\n\nSee console for all available details`);
                    // When ready to go live, remove the alert and show a success message within this page. For example:
                    // const element = document.getElementById('paypal-button-container');
                    // element.innerHTML = '<h3>Thank you for your payment!</h3>';
                    // Or go to another URL:  actions.redirect('thank_you.html');
                });
                }
            }).render('#paypal-button-container');
        </script>
        HTML;

        return $this->renderer->render('@basket/payment', compact('html', 'basket'));
    }
}
