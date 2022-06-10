<?php

namespace App\Shop\Action;

use App\Account\User;
use App\shop\Table\PurchaseTable;
use Framework\Auth;
use Framework\Auth\ForbiddenException;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class InvoiceAction
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

    public function __invoke(ServerRequestInterface $request)
    {
        $purchase = $this->purchaseTable->findWithProduct($request->getAttribute('id'));
        /** @var User */
        $user = $this->auth->getUser();
        if ($user->getId() !== $purchase->getUserId()) {
            throw new ForbiddenException('Vous n\'avez pas le droit de télécharger cette facture');
        }
        return $this->renderer->render('@shop/invoice', compact('purchase', 'user'));
    }
}
