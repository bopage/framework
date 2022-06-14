<?php
namespace Tests\App\Basket\Table;

use App\Basket\Basket;
use App\Basket\Table\OrderRowTable;
use App\Basket\Table\OrderTable;
use App\shop\Table\ProductTable;
use Tests\DatabaseTest;

class OrderTableTest extends DatabaseTest
{

    
    /**
     * orderTable
     *
     * @var OrderTable
     */
    private $orderTable;
    /**
     * productTable
     *
     * @var ProductTable
     */
    private $productTable;
    /**
     * orderRowTable
     *
     * @var OrderRowTable
     */
    private $orderRowTable;
    
    protected function setUp(): void
    {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $this->orderTable = new OrderTable($pdo);
        $this->productTable = new ProductTable($pdo);
        $this->orderRowTable = new OrderRowTable($pdo);
    }

    public function testCreateFromBasket()
    {
        $products = $this->productTable->makeQuery()->limit(10)->fetchAll();
        $basket = new Basket;
        $basket->addProduct($products[0]);
        $basket->addProduct($products[1], 2);
        $this->orderTable->createFromBasket($basket, [
            'vat' => 0,
            'country' => 'fr',
            'user_id' => 1,
            'stripe_id' => 1,
        ]);
        $order = $this->orderTable->find(1);
        $this->assertEquals($basket->getPrice(), $order->getPrice());
        $this->assertEquals(2, $this->orderRowTable->count());
        return $order;
    }

    public function testfindRows()
    {
        $order = $this->testCreateFromBasket();
        $this->orderTable->findRows([$order]);
        $this->assertCount(2, $order->getRows());
    }
}
