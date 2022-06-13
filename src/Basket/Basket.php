<?php
namespace App\Basket;

use App\Shop\Entity\Product;

class Basket
{
    protected $rows = [];
    
    /**
     * Ajout un produit dans le panier
     *
     * @param  Product $product
     * @param  int|null $quantity
     * @return void
     */
    public function addProduct(Product $product, ?int $quantity = null): void
    {
        if ($quantity === 0) {
            $this->removeProduct($product);
        } else {
            $row = $this->getRow($product);
            if ($row === null) {
                $row = new BasketRow;
                $row->setProduct($product);
                $this->rows[] = $row;
            } else {
                $row->setQuantity($row->getQuantity() + 1);
            }
            if ($quantity !== null) {
                $row->setQuantity($quantity);
            }
        }
    }
    
    /**
     * Supprime un produit du panier
     *
     * @param  Product $product
     * @return void
     */
    public function removeProduct(Product $product): void
    {
        $this->rows = array_filter($this->rows, function (BasketRow $row) use ($product) {
            return $row->getProductId() !== $product->getId();
        });
    }
    
    /**
     * Nombre de produit dans le panier
     *
     * @return int
     */
    public function count(): int
    {
        return array_reduce($this->rows, function ($count, BasketRow $row) {
            return $count + $row->getQuantity();
        }, 0);
    }
    
    /**
     * Revoie le prix du panier
     *
     * @return int
     */
    public function getPrice(): int
    {
        return array_reduce($this->rows, function ($total, BasketRow $row) {
            return $total + $row->getQuantity() * $row->getProduct()->getPrice();
        }, 0);
    }

    /**
     * Get the value of rows
     */
    public function getRows(): array
    {
        return $this->rows;
    }
    
    /**
     * Vérifie qu'une ligne du produit existe déjà
     *
     * @param  Product $product
     * @return BasketRow
     */
    protected function getRow(Product $product): ?BasketRow
    {
        /** @var BasketRow */
        foreach ($this->rows as $row) {
            if ($row->getProductId() === $product->getId()) {
                return $row;
            }
        }
        return null;
    }
    
    /**
     * Vide le panier
     *
     * @return void
     */
    public function empty()
    {
        $this->rows = [];
    }
}
