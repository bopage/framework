<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\Table;

class PostTable extends Table
{
    protected $table = 'posts';

    protected $entity = Post::class;

    protected function paginationQuery()
    {
        return "
            SELECT p.id, p.name, c.name as category_name
            FROM {$this->table} as p
            LEFT JOIN categories as c ON p.category_id = c.id
            ORDER BY created_at DESC";
    }
}
