<?php

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;
use PDO;

class PaginatedQuery implements AdapterInterface
{
    private $query;
    
    /**
     * __construct
     *
     * @param  Query $query
     * @return void
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public function getNbResults(): int
    {
        return $this->query->count();
    }

    public function getSlice(int $offset, int $length): iterable
    {
        $query = clone $this->query;
        return $query->limit($length, $offset)->fetchAll();
    }
}
