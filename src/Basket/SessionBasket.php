<?php
namespace App\Basket;

use App\Shop\Entity\Product;
use Framework\Session\SessionInterface;

class SessionBasket extends Basket
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $rows = $this->session->get('basket', []);
        $this->rows = array_map(function ($row) {
            $r = new BasketRow;
            $r->setProductId($row['id']);
            $r->setQuantity($row['quantity']);
            return $r;
        }, $rows);
    }

    public function addProduct(Product $product, ?int $quantity = null): void
    {
        parent::addProduct($product, $quantity);
        $this->persist();
    }

    public function removeProduct(Product $product): void
    {
        parent::removeProduct($product);
        $this->persist();
    }

    private function persist(): void
    {
        $this->session->set('basket', $this->serialize());
    }

    private function serialize(): array
    {
        return array_map(function (BasketRow $basketRow) {
            return [
                'id' => $basketRow->getProductId(),
                'quantity' => $basketRow->getQuantity()
            ];
        }, $this->rows);
    }
}
