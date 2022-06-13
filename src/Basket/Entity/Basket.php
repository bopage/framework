<?php
namespace App\Basket\Entity;

/**
 * Représente un panier
 */
class Basket
{

    
    /**
     * id
     *
     * @var int
     */
    private $id;
    
    /**
     * user_id
     *
     * @var int
     */
    private $user_id;

    /**
     * Get id
     *
     * @return  int
     */
    public function getId(): int
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
     * Get user_id
     *
     * @return  int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * Set user_id
     *
     * @param  int  $user_id  user_id
     *
     * @return  self
     */
    public function setUserId(int $user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }
}
