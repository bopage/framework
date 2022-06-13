<?php

namespace App\Basket\Table;

use App\Basket\Basket;
use App\Basket\BasketRow;
use App\Basket\Entity\Basket as EntityBasket;
use App\Shop\Entity\Product;
use App\shop\Table\ProductTable;
use Framework\Database\Hydrator;
use Framework\Database\Table;
use PDO;

/**
 * Représente le panier
 */
class BasketTable extends Table
{
    protected $table = 'baskets';

    protected $entity = EntityBasket::class;

    /**
     * productTable
     *
     * @var ProductTable
     */
    private $productTable;

    private $basketRowTable;

    public function __construct(PDO $pdo)
    {
        $this->productTable = new ProductTable($pdo);
        $this->basketRowTable =  new BasketRowTable($pdo);
        parent::__construct($pdo);
    }

    public function hydrateBasket(Basket $basket)
    {
        $rows = $basket->getRows();
        if (empty($rows)) {
            return null;
        }
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
    
    /**
     * Récupère le panier de l'utilisateur
     *
     * @param  int $userId
     * @return EntityBasket|null
     */
    public function findForUser(int $userId): ?EntityBasket
    {
        return $this->makeQuery()->where("user_id = $userId")->fetch() ?: null;
    }
    
    /**
     * Crée un panier pour l'utilisateur
     *
     * @param  int $userId
     * @return EntityBasket
     */
    public function createForUser(int $userId): EntityBasket
    {
        $params = [
            'user_id' => $userId
        ];
        $this->insert($params);
        $params['id'] = $this->getPdo()->lastInsertId();
        return Hydrator::hydrate($params, $this->entity);
    }
      
    /**
     * Ajout une nouvelle ligne au panier
     *
     * @param  EntityBasket $entityBasket
     * @param  Product $product
     * @param  int $quantity
     * @return BasketRow
     */
    public function addRow(EntityBasket $entityBasket, Product $product, int $quantity = 1): BasketRow
    {
        $params = [
            'basket_id' => $entityBasket->getId(),
            'product_id' => $product->getId(),
            'quantity' => $quantity
        ];
        $this->basketRowTable->insert($params);
        $params['id'] = $this->getPdo()->lastInsertId();
        /** @var BasketRow */
        $row = Hydrator::hydrate($params, $this->basketRowTable->getEntity());
        $row->setProduct($product);
        return $row;
    }
    
    /**
     * Rajoute la quantity pour une ligne du panier
     *
     * @param  BasketRow $row
     * @param  int $quantity
     * @return BasketRow
     */
    public function updateRowQuantity(BasketRow $row, int $quantity): BasketRow
    {
        $this->basketRowTable->update($row->getId(), ['quantity' => $quantity]);
        $row->setQuantity($quantity);
        return $row;
    }
    
    /**
     * Supprime une ligne du panier
     *
     * @param  BasketRow $row
     * @return void
     */
    public function deleteRow(BasketRow $row): void
    {
        $this->basketRowTable->delete($row->getId());
    }
    
    /**
     * Récupère l'ensemble des lignes du panier
     *
     * @param  EntityBasket $entityBasket
     * @return array
     */
    public function findRows(EntityBasket $entityBasket): array
    {
        return $this->basketRowTable
            ->makeQuery()
            ->where("basket_id = {$entityBasket->getId()}")
            ->fetchAll()
            ->toArray();
    }
}
