<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\PaginatedQuery;
use Framework\Database\Table;
use Pagerfanta\Pagerfanta;

class PostTable extends Table
{
    protected $table = 'posts';

    protected $entity = Post::class;

    public function findPaginetedPublic(int $perPage, int $currentPage): Pagerfanta
    {
        $paginatedQuery =  new PaginatedQuery(
            $this->getPdo(),
            "SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM posts as p
            LEFT JOIN categories as c ON c.id = p.category_id
            ORDER BY created_at DESC",
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );

        return (new Pagerfanta($paginatedQuery))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findPaginetedPublicForCategory(int $perPage, int $currentPage, int $categoryId): Pagerfanta
    {
        $paginatedQuery =  new PaginatedQuery(
            $this->getPdo(),
            "SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM posts as p
            LEFT JOIN categories as c ON c.id = p.category_id
            WHERE p.category_id = :category
            ORDER BY created_at DESC",
            "SELECT COUNT(id) FROM {$this->table} WHERE category_id = :category",
            $this->entity,
            ["category" => $categoryId]
        );

        return (new Pagerfanta($paginatedQuery))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    protected function paginationQuery()
    {
        return "
            SELECT p.id, p.name, c.name as category_name
            FROM {$this->table} as p
            LEFT JOIN categories as c ON p.category_id = c.id
            ORDER BY created_at DESC";
    }
    
    /**
     * Récupère l'article et la categories associée
     *
     * @param  int $id
     * @return mixed
     */
    public function findWithCategory(int $id)
    {
        return $this->fecthOrFail("
        SELECT p.*, c.name as category_name, c.slug as category_slug
        FROM {$this->table} as p
        LEFT JOIN categories as c ON c.id = p.category_id
        WHERE p.id = ?
        ", [$id]);
    }
}
