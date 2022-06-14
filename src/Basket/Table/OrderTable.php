<?php

namespace App\Basket\Table;

use App\Auth\User;
use App\Basket\Basket;
use App\Basket\BasketRow;
use App\Basket\Entity\Order;
use App\Basket\Entity\OrderRow;
use App\Shop\Entity\Product;
use Framework\Database\Query;
use Framework\Database\Table;

class OrderTable extends Table
{
    protected $table = 'orders';
    protected $entity = Order::class;

    private $orderRowTable;

    /**
     * findForUser Toutes les commandes liées à l'utilisateur
     *
     * @param  User $user
     * @return Query
     */
    public function findForUser(User $user): Query
    {
        return $this->makeQuery()->where("user_id = {$user->getId()}");
    }

    /**
     * findRows Ajoute les lignes correspondantes à la commande
     *
     * @param  Order[] $orders
     * @return void
     */
    public function findRows($orders)
    {
        $ordersId = [];
        foreach ($orders as $order) {
            $ordersId[] = $order->getId();
        }
        $rows = $this->getOrderRowTable()->makeQuery()
            ->where('o.order_id IN (' . implode(',', $ordersId) . ')')
            ->join('products as p', 'p.id = o.product_id')
            ->select('o.*', 'p.name as productName', 'p.slug as ProductSlug')
            ->fetchAll();
        /** @var OrderRow */
        foreach ($rows as $row) {
            foreach ($orders as $order) {
                if ($order->getId() === $row->getOrderId()) {
                    $product = new Product;
                    $product->setId($row->getProductId());
                    $product->setName($row->productName);
                    $product->setSlug($row->productSlug);
                    $row->setProduct($product);
                    $order->addRow($row);
                    break;
                }
            }
        }
        return $rows;
    }

    /**
     * Création de la commande
     *
     * @param  Basket $basket
     * @param  array $params
     * @return void
     */
    public function createFromBasket(Basket $basket, array $params = []): void
    {
        $params['price'] = $basket->getPrice();
        $params['created_at'] = date('Y-m-d H:i:s');
        $this->getPdo()->beginTransaction();
        $this->insert($params);
        $orderId = $this->getPdo()->lastInsertId();
        /** @var BasketRow */
        foreach ($basket->getRows() as $row) {
            $this->getOrderRowTable()->insert([
                'order_id' => $orderId,
                'price' => $row->getProduct()->getPrice(),
                'product_id' => $row->getProductId(),
                'quantity' => $row->getQuantity()
            ]);
        }
        $this->getPdo()->commit();
    }

    /**
     * Get the value of orderRowTable
     */
    public function getOrderRowTable(): OrderRowTable
    {
        if ($this->orderRowTable === null) {
            $this->orderRowTable = new OrderRowTable($this->getPdo());
        }
        return $this->orderRowTable;
    }
}
