<?php

namespace App\Basket\Action;

use App\Basket\Basket;
use App\Basket\Table\BasketTable;
use App\shop\Table\ProductTable;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectBackResponse;
use Psr\Http\Message\ServerRequestInterface;

class BasketAction
{
    private $basket;
    private $renderer;
    private $basketTable;

    public function __construct(
        Basket $basket,
        RendererInterface $renderer,
        BasketTable $basketTable
    ) {
        $this->basket = $basket;
        $this->basketTable = $basketTable;
        $this->renderer = $renderer;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'GET') {
            return  $this->show();
        } elseif ($request->getMethod() === 'POST') {
            $product = $this->basketTable->getProductTable()->find((int)$request->getAttribute('id'));
            $params = $request->getParsedBody();
            $this->basket->addProduct($product, $params['quantity'] ?? null);
            return new RedirectBackResponse($request);
        }
    }

    private function show()
    {
        $this->basketTable->hydrateBasket($this->basket);
        return $this->renderer->render('@basket/show', [
            'basket' => $this->basket
        ]);
    }
}
