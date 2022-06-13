<?php

namespace Graphikart;

use Psr\EventManager\EventInterface;

class Event implements EventInterface
{

    
    /**
     * name
     *
     * @var string
     */
    private $name = "";
    
    /**
     * target
     *
     * @var mixed
     */
    private $target;

    
    /**
     * params
     *
     * @var array
     */
    private $params = [];

    
    /**
     * propagationStopped
     *
     * @var bool
     */
    private $propagationStopped = false;

    public function getName(): string
    {
        return $this->name;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $name)
    {
        return $this->params[$name] ?: null;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setTarget($target): void
    {
        $this->target = $target;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function stopPropagation(bool $flag): void
    {
        $this->propagationStopped = $flag;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
