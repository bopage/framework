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

    public $image;


    public function setCreatedAt(string $datetime)
    {
        if (is_string($this->createdAt)) {
            $this->createdAt = new DateTime($datetime);
        }
    }

    public function setUpdateAt(string $datetime)
    {
        if (is_string($this->updatedAt)) {
            $this->updatedAt = new DateTime($datetime);
        }
    }

    public function getThumb()
    {
        return '/uploads/posts/' . $this->image;
    }
}
