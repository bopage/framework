<?php

namespace App\Blog\Entity;

use DateTime;

class Post
{
    public $id;

    public $name;

    public $slug;

    public $content;

    public $createdAt;

    public $updatedAt;


    public function setCreatedAt(string $datetime)
    {
        if ($this->createdAt) {
            $this->createdAt = new DateTime($datetime);
        }
    }

    public function setUpdateAt(string $datetime)
    {
        if ($this->updatedAt) {
            $this->updatedAt = new DateTime($datetime);
        }
    }
}
