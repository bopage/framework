<?php

namespace Framework\Router;

/**
 * Route
 * Les paramètres d'une route
 */
class Route
{

       
    /**
     * name
     *
     * @var string
     */
    private $name;
    
    /**
     * callback
     *
     * @var callable
     */
    private $callback;
    
    /**
     * params
     *
     * @var array
     */
    private $params;

    public function __construct(string $name, callable $callback, array $params)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->params = $params;
    }

    /**
     * Le nom de la route
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Le traitement effectué par la route
     *
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }
    
    /**
     * Le slug et l'id de la route
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
