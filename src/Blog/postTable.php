<?php

namespace App\Blog;

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
     * @return array
     */
    public function findPagineted(): array
    {
        return $this->pdo
            ->query('SELECT * FROM posts ORDER BY created_at DESC LIMIT 10')
            ->fetchAll();
    }

    /**
     * Récupère un article
     *
     * @param  int $id
     * @return stdClass
     */
    public function find(int $id): stdClass
    {
        $query = $this->pdo->prepare('SELECT * FROM posts WHERE id= ?');
        $query->execute([$id]);
        return $query->fetch();
    }
}
