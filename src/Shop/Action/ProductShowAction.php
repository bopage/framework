<?php

namespace App\Shop\Action;

use App\shop\Table\ProductTable;
use App\shop\Table\PurchaseTable;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductShowAction
{
    private $renderer;

    private $productTable;
    private $purchaseTable;
    private $auth;

    public function __construct(
        RendererInterface $renderer,
        ProductTable $productTable,
        PurchaseTable $purchaseTable,
        Auth $auth
    ) {
        $this->renderer = $renderer;
        $this->productTable = $productTable;
        $this->purchaseTable = $purchaseTable;
        $this->auth = $auth;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $product = $this->productTable->findBy('slug', $request->getAttribute('slug'));
        $download = false;
        $user = $this->auth->getUser();
        if ($user !== null && $this->purchaseTable->findFor($product, $user)) {
            $download = true;
        }
        return $this->renderer->render('@shop/show', compact('product', 'download'));
    }
}
