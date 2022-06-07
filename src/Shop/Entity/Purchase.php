<?php
namespace App\Shop\Entity;

use DateTime;

/**
 * Purchase
 * Représente un achat
 */
class Purchase
{
    private $id;

    private $userId;

    private $productId;

    private $price;

    private $vat;

    private $country;

    private $createdAt;

    private $stripeId;


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
     * Get the value of userId
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @return  self
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;

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
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @return  self
     */
    public function setPrice(float $price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get the value of vat
     */
    public function getVat(): ?float
    {
        return $this->vat;
    }

    /**
     * Set the value of vat
     *
     * @return  self
     */
    public function setVat(float $vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get the value of country
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Set the value of country
     *
     * @return  self
     */
    public function setCountry(string $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of stripeId
     */
    public function getStripeId(): ?int
    {
        return $this->stripeId;
    }

    /**
     * Set the value of stripeId
     *
     * @return  self
     */
    public function setStripeId(int $stripeId)
    {
        $this->stripeId = $stripeId;

        return $this;
    }
}
