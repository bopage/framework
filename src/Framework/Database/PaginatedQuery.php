<?php

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;
use PDO;

class PaginatedQuery implements AdapterInterface
{
    private $pdo;
    private $query;
    private $queryCount;
    private $entity;
    
    /**
     * __construct
     *
     * @param  PDO $pdo
     * @param  string $query Requête permettant de récupér X résultats
     * @param  string $queryCount Requête permettane de compter tous les enregistrements
     * @param  string $entity Object de récupération des données
     * @return void
     */
    public function __construct(PDO $pdo, string $query, string $queryCount, string $entity)
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->queryCount = $queryCount;
        $this->entity = $entity;
    }

    public function getNbResults(): int
    {
        return $this->pdo->query($this->queryCount)->fetchColumn();
    }

    public function getSlice(int $offset, int $length): iterable
    {
        $prepare = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');
        $prepare->bindParam('offset', $offset, PDO::PARAM_INT);
        $prepare->bindParam('length', $length, PDO::PARAM_INT);
        $prepare->execute();
        $prepare->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        return $prepare->fetchAll();
    }
}
