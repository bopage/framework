<?php
namespace App\Basket\Entity;

use DateTime;

/**
 * Order Représente une commande
 */
class Order
{

    
    /**
     * id
     *
     * @var int
     */
    private $id;
    /**
     * userId
     *
     * @var int|null
     */
    private $userId;
    /**
     * price
     *
     * @var float
     */
    private $price;
    /**
     * vat
     *
     * @var float
     */
    private $vat;
    /**
     * country
     *
     * @var string
     */
    private $country;
    /**
     * created_at
     *
     * @var DateTime
     */
    private $createdAt;
    /**
     * stripeId
     *
     * @var int
     */
    private $stripeId;
    /**
     * rows
     *
     * @var OrderRow[]
     */
    private $rows = [];

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
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get userId
     *
     * @return  int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * Set userId
     *
     * @param  int|null  $userId  userId
     *
     * @return  self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get price
     *
     * @return  float
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * Set price
     *
     * @param  float  $price  price
     *
     * @return  self
     */
    public function setPrice(float $price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get vat
     *
     * @return  float
     */
    public function getVat(): ?float
    {
        return $this->vat;
    }

    /**
     * Set vat
     *
     * @param  float  $vat  vat
     *
     * @return  self
     */
    public function setVat(float $vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get country
     *
     * @return  string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Set country
     *
     * @param  string  $country  country
     *
     * @return  self
     */
    public function setCountry(string $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return  mixed
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set created_at
     *
     * @param  mixed  $created_at  created_at
     *
     * @return  self
     */
    public function setCreatedAt($createdAt)
    {
        if (is_string($createdAt)) {
            $this->createdAt = new DateTime($createdAt);
        } else {
            $this->createdAt = $createdAt;
        }
        return $this;
    }

    /**
     * Get stripeId
     *
     * @return  int
     */
    public function getStripeId(): ?int
    {
        return $this->stripeId;
    }

    /**
     * Set stripeId
     *
     * @param  int  $stripeId  stripeId
     *
     * @return  self
     */
    public function setStripeId(int $stripeId)
    {
        $this->stripeId = $stripeId;

        return $this;
    }

    /**
     * Get rows
     *
     * @return  OrderRow[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * Set rows
     *
     * @param  OrderRow[]  $rows  rows
     *
     * @return  self
     */
    public function setRows(array $rows)
    {
        $this->rows = $rows;

        return $this;
    }
    
    /**
     * Rajoute une ligne
     *
     * @param  mixed $row
     * @return void
     */
    public function addRow($row)
    {
        $this->rows[] = $row;
    }
}
