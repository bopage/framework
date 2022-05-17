<?php

namespace Framework\Database;

use Exception;
use ArrayAccess;
use Iterator;

class QueryResult implements ArrayAccess, Iterator
{
    private $records;

    private $entity;
    
    private $index = 0;

    private $hydrateRecords = [];

    public function __construct(array $records, ?string $entity = null)
    {
        $this->records = $records;
        $this->entity = $entity;
    }

    public function get(int $index)
    {
        if ($this->entity) {
            if (!isset($this->hydrateRecords[$index])) {
                $this->hydrateRecords[$index] =  Hydrator::hydrate($this->records[$index], $this->entity);
            }
            return $this->hydrateRecords[$index];
        }
        return $this->entity;
    }

    public function current(): mixed
    {
        return $this->get($this->index);
    }

    public function next(): void
    {
        $this->index++;
    }

    public function key(): mixed
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return isset($this->records[$this->index]);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->records[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new Exception('can\'t changer records');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new Exception('can\'t changer records');
    }
}
