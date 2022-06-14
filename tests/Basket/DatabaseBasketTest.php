<?php
namespace Tests\App\Basket;

use App\Basket\Basket;
use App\Basket\DatabaseBasket;
use App\Basket\Table\BasketRowTable;
use App\Basket\Table\BasketTable;
use PHPUnit\Framework\TestCase;
use Tests\DatabaseTest;

class DatabaseBasketTest extends DatabaseTest
{

    
    /**
     * basketTable
     *
     * @var BasketTable
     */
    private $basketTable;

    private $rowTable;

    private $basket;

    protected function setUp(): void
    {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $this->basketTable = new BasketTable($pdo);
        $this->rowTable = new BasketRowTable($pdo);
        $this->basket = new DatabaseBasket(2, $this->basketTable);
    }

    public function testAddProduct()
    {
        $products = $this->basketTable->getProductTable()
            ->makeQuery()
            ->limit(2)
            ->fetchAll();
        $this->basket->addProduct($products[0]);
        $this->basket->addProduct($products[0]);
        $this->basket->addProduct($products[1], 2);
        $this->assertEquals(2, $this->rowTable->count());
    }

    public function testPersistence()
    {
        $products = $this->basketTable->getProductTable()
            ->makeQuery()
            ->limit(2)
            ->fetchAll();
        $this->basket->addProduct($products[0], 2);
        $this->basket->addProduct($products[1], 3);
        $basket = new DatabaseBasket(2, $this->basketTable);
        $this->assertEquals(5, $basket->count());
    }

    public function testRemoveProduct()
    {
        $products = $this->basketTable->getProductTable()
            ->makeQuery()
            ->limit(2)
            ->fetchAll();
        $this->basket->addProduct($products[0]);
        $this->basket->addProduct($products[1], 2);
        $this->basket->removeProduct($products[0]);
        $this->assertEquals(1, $this->rowTable->count());
    }

    public function testMergeBasket()
    {
        $products = $this->basketTable->getProductTable()
            ->makeQuery()
            ->limit(2)
            ->fetchAll();
        $this->basket->addProduct($products[0]);
        $basket = new Basket;
        $basket->addProduct($products[0], 3);
        $basket->addProduct($products[1], 2);

        $this->basket->merge($basket);

        $this->assertEquals(6, $this->basket->count());
        $this->assertEquals(4, $this->basket->getRows()[0]->getQuantity());
        $this->assertEquals(2, $this->basket->getRows()[1]->getQuantity());
    }

    public function testEmptyProduct()
    {
        $products = $this->basketTable->getProductTable()
            ->makeQuery()
            ->limit(2)
            ->fetchAll();
        $this->basket->addProduct($products[0]);
        $this->basket->addProduct($products[0]);
        $this->basket->addProduct($products[1], 2);
        $this->basket->empty();
        $this->assertEquals(0, $this->rowTable->count());
    }
}
