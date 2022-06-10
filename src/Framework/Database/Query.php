<?php

namespace Framework\Database;

use IteratorAggregate;
use Pagerfanta\Pagerfanta;
use PDO;
use Traversable;

class Query implements IteratorAggregate
{
    private $select;

    private $from;

    private $where = [];

    private $pdo;

    private $params = [];

    private $order = [];

    private $limit;

    private $joins;

    private $entity;


    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * Définit la table à traiter
     *
     * @param  string $table
     * @param  string|null $alias
     * @return self
     */
    public function from(string $table, ?string $alias = null): self
    {
        if ($alias) {
            $this->from[$table] = $alias;
        } else {
            $this->from[] = $table;
        }
        return $this;
    }

    /**
     * Sélectionne les éléments à récupèrer
     *
     * @param  string $fields
     * @return self
     */
    public function select(string ...$fields): self
    {
        $this->select = $fields;
        return $this;
    }

    /**
     * Récupère le nombre d'éléments
     *
     * @return int
     */
    public function count(): int
    {
        $query = clone $this;
        $table = current($this->from);
        return $query->select("COUNT($table.id)")->execute()->fetchColumn();
    }

    /**
     * Définit les paramètres pour les requêtes préparées
     *
     * @param  array $params
     * @return self
     */
    public function params(array $params): self
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    /**
     * Spécifie une limite
     *
     * @param  string $limit
     * @return self
     */
    public function limit(int $length, int $offset): self
    {
        $this->limit = "$offset, $length";
        return $this;
    }

    /**
     * Spécifie l'ordre de récupération
     *
     * @param  string $orders
     * @return self
     */
    public function order(string $orders): self
    {
        $this->order[] = $orders;
        return $this;
    }

    /**
     * Ajoute une liaision
     *
     * @param  string $table
     * @param  string $conditions
     * @param  string $type
     * @return self
     */
    public function join(string $table, string $conditions, string $type = 'left'): self
    {
        $this->joins[$type][] = [$table, $conditions];
        return $this;
    }

    /**
     * Définit la condition de récupération
     *
     * @param  string $conditions
     * @return self
     */
    public function where(string ...$conditions): self
    {
        $this->where = array_merge($this->where, $conditions);
        return $this;
    }

    /**
     * Définit l'entité de récupération
     *
     * @param  string $entity
     * @return self
     */
    public function into(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }
    
    /**
     * Récupère un résultat
     *
     * @return mixed
     */
    public function fetch()
    {
        $record = $this->execute()->fetch(PDO::FETCH_ASSOC);
        if ($record === false) {
            return false;
        }
        if ($this->entity) {
            return Hydrator::hydrate($record, $this->entity);
        }
        return $record;
    }

    /**
     * Récupère un résultat
     *
     * @return mixed
     */
    public function fetchColumn(int $columnNumber = 0)
    {
        return $this->execute()->fetchColumn($columnNumber);
    }
    
    /**
     * Retourne un résultat ou renvoie une exeption
     *
     * @return mixed
     */
    public function fetchOrFail()
    {
        $record = $this->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }

    /**
     * Récupère tous les enrgistrements
     *
     * @return QueryResult
     */
    public function fetchAll(): QueryResult
    {
        return new QueryResult(
            $this->execute()->fetchAll(PDO::FETCH_ASSOC),
            $this->entity
        );
    }
    
    /**
     * Pagine les éléments
     *
     * @param  int $perpage
     * @param  int $currentPage
     * @return Pagerfanta
     */
    public function paginate(int $perpage, int $currentPage = 1): Pagerfanta
    {
        $paginator = new PaginatedQuery($this);
        return (new Pagerfanta($paginator))->setMaxPerPage($perpage)->setCurrentPage($currentPage);
    }


    public function __toString()
    {
        $parts = ['SELECT'];
        if ($this->select) {
            $parts[] = join(', ', $this->select);
        } else {
            $parts[] = '*';
        }
        $parts[] = 'FROM';
        $parts[] = $this->builfrom();

        if (!empty($this->joins)) {
            foreach ($this->joins as $type => $joins) {
                foreach ($joins as [$table, $conditions]) {
                    $parts[] = strtoupper($type) . " JOIN $table ON $conditions";
                }
            }
        }

        if (!empty($this->where)) {
            $parts[] = 'WHERE';
            $parts[] = "(" . join(') AND (', $this->where) . ")";
        }

        if (!empty($this->order)) {
            $parts[] = "ORDER BY";
            $parts[] = join(', ', $this->order);
        }

        if ($this->limit) {
            $parts[] = "LIMIT " . $this->limit;
        }

        return join(' ', $parts);
    }

    private function builfrom()
    {
        $from = [];
        foreach ($this->from as $key => $value) {
            if (is_string($key)) {
                $from[] = "$key as $value";
            } else {
                $from[] = $value;
            }
        }

        return join(', ', $from);
    }

    private function execute()
    {
        $query = $this->__toString();
        if (!empty($this->params)) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->params);
            return $statement;
        }
        return $this->pdo->query($query);
    }

    public function getIterator(): Traversable
    {
        return $this->fetchAll();
    }
}
