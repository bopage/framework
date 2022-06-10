<?php

namespace App\Shop\Action;

use App\shop\Table\PurchaseTable;
use Framework\Auth;
use Framework\Renderer\RendererInterface;

class PurchaseListingAction
{
    private $renderer;
    private $purchaseTable;
    private $auth;

    public function __construct(
        RendererInterface $renderer,
        PurchaseTable $purchaseTable,
        Auth $auth
    ) {
        $this->renderer = $renderer;
        $this->purchaseTable = $purchaseTable;
        $this->auth = $auth;
    }

    public function __invoke()
    {
        $purchases = $this->purchaseTable->findForUser($this->auth->getUser());
        return $this->renderer->render('@shop/purchases', compact('purchases'));
    }
}
