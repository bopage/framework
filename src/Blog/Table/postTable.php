<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\PaginatedQuery;
use Framework\Database\Query;
use Framework\Database\Table;
use Pagerfanta\Pagerfanta;

class PostTable extends Table
{
    protected $table = 'posts';

    protected $entity = Post::class;

    public function findAll(): Query
    {
        $category = new CategoryTable($this->getPdo());
        return ($this->makeQuery())
                ->join($category->getTable() . ' as c', 'c.id = p.category_id')
                ->select('p.*', 'c.slug as category_slug', 'c.name as category_name')
                ->order('p.created_at DESC');
    }

    public function findPublic(): Query
    {
        return $this->findAll()
            ->where('p.published = 1')
            ->where('p.created_at < NOW()');
    }

    public function findPublicForcategory(int $id): Query
    {
        return $this->findPublic()->where("p.category_id = $id");
    }

    public function findWithCategory(int $postId): Post
    {
        return $this->findPublic()->where("p.id = $postId")->fetch();
    }
}
