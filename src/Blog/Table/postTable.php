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
    public function find(int $id): ?Post
    {
        $query = $this->pdo->prepare('SELECT * FROM posts WHERE id= ?');
        $query->execute([$id]);
        $query->setFetchMode(PDO::FETCH_CLASS, Post::class);
        return $query->fetch() ?: null;
    }
    
    /**
     * Persiste les enregistrement en base de donnée
     *
     * @param  int $id
     * @param  array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fiedQuery = join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
        $params['id'] = $id;
        $statement = $this->pdo->prepare("UPDATE posts SET $fiedQuery WHERE id = :id");
        return $statement->execute($params);
    }
    
    /**
     * Insert les données sur la base de donnée
     *
     * @param  array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $value = array_map(function ($field) {
            return ":$field";
        }, $fields);
        $statement = $this->pdo->prepare("
        INSERT INTO posts 
        (" . join(',', $fields) .")
         VALUES (".join(',', $value) .") 
         ");
        return $statement->execute($params);
    }
    
    /**
     * Supprime un enregistrement
     *
     * @param  int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE  FROM posts WHERE id = ?");
        return $statement->execute([$id]);
    }

    private function buildFieldQuery(array $params)
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }
}
