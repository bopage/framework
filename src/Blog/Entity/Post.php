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
        if (is_string($datetime)) {
            $this->createdAt = new DateTime($datetime);
        }
    }

    public function setUpdateAt(string $datetime)
    {
        if (is_string($datetime)) {
            $this->updatedAt = new DateTime($datetime);
        }
    }

    public function getThumb()
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
        return '/uploads/posts/' . $filename . '_thumb.' . $extension;
    }

    public function getImageUrl()
    {
        return '/uploads/posts/' . $this->image;
    }
}
