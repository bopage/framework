<?php
namespace Framework\Entity;

use DateTime;

trait TimeStamp
{
    private $createdAt;
    private $updatedAt;

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt($datetime): self
    {
        if (is_string($datetime)) {
            $this->createdAt = new DateTime($datetime);
        } else {
            $this->createdAt = $datetime;
        }
        return $this;
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt($datetime): self
    {
        if (is_string($datetime)) {
            $this->updatedAt = new DateTime($datetime);
        } else {
            $this->updatedAt = $datetime;
        }
        return $this;
    }
}
