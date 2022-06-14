<?php
namespace App\Basket\Entity;

use App\Shop\Entity\Product;

/**
 * OrderRow Représente une ligne dans la commande
 */
class OrderRow
{
    private $id;
    private $orderId;
    private $productId;
    private $price;
    private $quantity;
    /**
     * product
     *
     * @var Product
     */
    private $product;

    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of orderId
     */
    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    /**
     * Set the value of orderId
     *
     * @return  self
     */
    public function setOrderId(int $orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get the value of productId
     */
    public function getProductId(): ?int
    {
        return $this->productId;
    }

    /**
     * Set the value of productId
     *
     * @return  self
     */
    public function setProductId(int $productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get the value of price
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @return  self
     */
    public function setPrice(?float $price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get the value of quantity
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * Set the value of quantity
     *
     * @return  self
     */
    public function setQuantity(?int $quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get product
     *
     * @return  Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * Set product
     *
     * @param  Product  $product  product
     *
     * @return  self
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }
}
