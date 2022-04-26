<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\PaginatedQuery;
use Pagerfanta\Pagerfanta;
use PDO;
use stdClass;

class PostTable
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

       
    /**
     * Pagine les articles
     *
     * @return Pagerfanta
     */
    public function findPagineted(int $perPage, int $currentPage): Pagerfanta
    {
        $paginatedQuery=  new PaginatedQuery(
            $this->pdo,
            'SELECT * FROM posts ORDER BY created_at DESC',
            'SELECT COUNT(id) FROM posts',
            Post::class
        );

        return (new Pagerfanta($paginatedQuery))
                ->setMaxPerPage($perPage)
                ->setCurrentPage($currentPage);
    }

    /**
     * Récupère un article
     *
     * @param  int $id
     * @return stdClass
     */
    public function find(int $id): Post
    {
        $query = $this->pdo->prepare('SELECT * FROM posts WHERE id= ?');
        $query->execute([$id]);
        $query->setFetchMode(PDO::FETCH_CLASS, Post::class);
        return $query->fetch();
    }
}
