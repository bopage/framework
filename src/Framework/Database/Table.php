<?php

namespace Framework\Database;

use PDO;
use stdClass;

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
     * @var string
     */
    protected $entity = stdClass::class;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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
     *
     * @return Query
     */
    public function makeQuery(): Query
    {
        return (new Query($this->pdo))->from($this->table, $this->table[0])->into($this->entity);
    }


    /**
     * Récupère tous les engistrements
     *
     * @return Query
     */
    public function findAll(): Query
    {
        return $this->makeQuery();
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
        return $this->makeQuery()->where("$field = :field")->params(["field" => $value])->fetchOrFail();
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
        return $this->makeQuery()->where("id = $id")->fetchOrFail();
    }

    /**
     * Compte le nombre d'enregistrement
     *
     * @return mixed
     */
    public function count()
    {
        return $this->makeQuery()->count();
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
}
