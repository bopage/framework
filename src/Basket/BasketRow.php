<?php
namespace App\Basket;

use App\Shop\Entity\Product;

class BasketRow
{
    
    /**
     * id
     *
     * @var int
     */
    private $id;
    
    /**
     * product
     *
     * @var Product
     */
    private $product;
    
    /**
     * productId
     *
     * @var int
     */
    private $productId;
    
    /**
     * quantity
     *
     * @var int
     */
    private $quantity = 1;

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
    public function setProduct(Product $product): self
    {
        $this->product = $product;
        $this->productId = $product->getId();

        return $this;
    }

    /**
     * Get productId
     *
     * @return  int|null
     */
    public function getProductId(): ?int
    {
        return $this->productId;
    }

    /**
     * Set productId
     *
     * @param  int $productId  productId
     *
     * @return  self
     */
    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return  int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Set quantity
     *
     * @param  int|null  $quantity  quantity
     *
     * @return  self
     */
    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get id
     *
     * @return  int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param  int  $id  id
     *
     * @return  self
     */
    public function setId(?int $id = null)
    {
        $this->id = $id;

        return $this;
    }
}
