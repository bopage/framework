<?php
namespace App\Basket\Action;

use App\Basket\Table\OrderTable;
use Framework\Auth;
use Framework\Auth\ForbiddenException;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class OrderInvoiceAction
{
    private $renderer;
    private $auth;
    private $orderTable;

    public function __construct(RendererInterface $renderer, Auth $auth, OrderTable $orderTable)
    {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->orderTable = $orderTable;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $order = $this->orderTable->find($request->getAttribute('id'));
        $this->orderTable->findRows([$order]);
        /** @var User */
        $user = $this->auth->getUser();
        if ($user->getId() !== $order->getUserId()) {
            throw new ForbiddenException('Vous ne pouvez pas télécharger cette facture');
        }
        return $this->renderer->render('@basket/invoice', compact('order', 'user'));
    }
}
