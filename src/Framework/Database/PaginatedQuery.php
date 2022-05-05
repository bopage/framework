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
    private $param;
    
    /**
     * __construct
     *
     * @param  PDO $pdo
     * @param  string $query Requête permettant de récupér X résultats
     * @param  string $queryCount Requête permettane de compter tous les enregistrements
     * @param  string|null $entity Object de récupération des données
     * @param  array|null $param Correspond au tableau d'option pour les requêtes préparée
     * @return void
     */
    public function __construct(
        PDO $pdo,
        string $query,
        string $queryCount,
        ?string $entity,
        ?array $param = []
    ) {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->queryCount = $queryCount;
        $this->entity = $entity;
        $this->param = $param;
    }

    public function getNbResults(): int
    {
        if (!empty($this->param)) {
            $query = $this->pdo->prepare($this->queryCount);
            $query->execute($this->param);

            return $query->fetchColumn();
        }
        return $this->pdo->query($this->queryCount)->fetchColumn();
    }

    public function getSlice(int $offset, int $length): iterable
    {
        $prepare = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');
        foreach ($this->param as $key => $param) {
            $prepare->bindParam($key, $param);
        }
        $prepare->bindParam('offset', $offset, PDO::PARAM_INT);
        $prepare->bindParam('length', $length, PDO::PARAM_INT);
        $prepare->execute();
        if ($this->entity) {
            $prepare->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        return $prepare->fetchAll();
    }
}
