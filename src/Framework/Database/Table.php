<?php

namespace Framework\Database;

use Framework\Database\PaginatedQuery;
use Pagerfanta\Pagerfanta;
use PDO;

class Table
{

    /**
     * pdo
     *
     * @var PDO
     */
    private $pdo;

    /**
     * Table à utiliser en BDD
     *
     * @var string
     */
    protected $table;

    /**
     * Entitée à utiliser pour rédupérer les données
     *
     * @var string|null
     */
    protected $entity;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    /**
     * Pagine les éléments
     *
     * @return Pagerfanta
     */
    public function findPagineted(int $perPage, int $currentPage): Pagerfanta
    {
        $paginatedQuery =  new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );

        return (new Pagerfanta($paginatedQuery))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * Les élément a paginer
     *
     * @return string
     */
    protected function paginationQuery()
    {
        return "SELECT * FROM {$this->table}";
    }

    /**
     * Récupère une liste clef valeur de nos enregistrement
     *
     * @return array
     */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }

        return $list;
    }


    /**
     * Récupère tous les engistrements
     *
     * @return array
     */
    public function findAll(): array
    {
        $statement = $this->pdo->query("SELECT * FROM {$this->table}");
        if ($this->entity) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $statement->setFetchMode(PDO::FETCH_OBJ);
        }

        return $statement->fetchAll();
    }
    
    /**
     * Récupère une ligne par rapport à un champ
     *
     * @param  string $field
     * @param  string $value
     * @return object
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value): object
    {
        return $this->fecthOrFail("SELECT * FROM {$this->table} WHERE $field = ?", [$value]);
    }

    /**
     * Récupère un élément
     *
     * @param  int $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        return $this->fecthOrFail("SELECT * FROM {$this->table} WHERE id= ?", [$id]);
    }
    
    /**
     * Compte le nombre d'enregistrement
     *
     * @return mixed
     */
    public function count()
    {
        return $this->fecthColumn("SELECT COUNT(id) FROM {$this->table}");
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
        $statement = $this->pdo->prepare("UPDATE {$this->table} SET $fiedQuery WHERE id = :id");
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
        $fields =  array_keys($params);
        $value =  join(',', array_map(function ($field) {
            return ":$field";
        }, $fields));
        $fields = join(',', $fields);
        $statement = $this->pdo->prepare("INSERT INTO {$this->table} ($fields) VALUES ($value)");
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
        $statement = $this->pdo->prepare("DELETE  FROM {$this->table} WHERE id = ?");
        return $statement->execute([$id]);
    }

    private function buildFieldQuery(array $params)
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    /**
     * Get table à utiliser en BDD
     *
     * @return  string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Get entitée à utiliser pour rédupérer les données
     *
     * @return  string|null
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * Vérifie qu'un enregistrement exist
     *
     * @param  int $id
     * @return bool
     */
    public function exist(int $id): bool
    {
        $statement = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $statement->execute([$id]);
        return $statement->fetchColumn() !== false;
    }


    /**
     * Get pdo
     *
     * @return  PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
    
    /**
     * Permet d'exécuter une requête et de récupérer une ligne dans un champ
     *
     * @param  string $query
     * @param  array|null $param
     * @return mixed
     */
    protected function fecthOrFail(string $query, ?array $param = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($param);
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        $record = $query->fetch();

        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }
    
    /**
     * Récupère la première colonne
     *
     * @param  string $query
     * @param  array $param
     * @return void
     */
    protected function fecthColumn(string $query, ?array $param = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($param);
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
         return $query->fetchColumn();
    }
}
