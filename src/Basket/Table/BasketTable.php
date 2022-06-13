<?php

namespace App\Basket\Table;

use App\Basket\Basket;
use App\Basket\BasketRow;
use App\Shop\Entity\Product;
use App\shop\Table\ProductTable;
use PDO;

class BasketTable
{
    /**
     * productTable
     *
     * @var ProductTable
     */
    private $productTable;

    public function __construct(PDO $pdo)
    {
        $this->productTable = new ProductTable($pdo);
    }

    public function hydrateBasket(Basket $basket)
    {
        $rows = $basket->getRows();
        $ids = array_map(function (BasketRow $basketRow) {
            return $basketRow->getProductId();
        }, $rows);
        /** @var Product[] */
        $products = $this->productTable->makeQuery()
            ->where('id IN (' . implode(',', $ids) . ')')
            ->fetchAll();
        $productsById = [];
        foreach ($products as $product) {
            $productsById[$product->getId()] = $product;
        }
        foreach ($rows as $row) {
            $row->setProduct($productsById[$row->getProductId()]);
        }
    }

    /**
     * Get productTable
     *
     * @return  ProductTable
     */
    public function getProductTable()
    {
        return $this->productTable;
    }
}
