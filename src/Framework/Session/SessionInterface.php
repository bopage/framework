<?php

namespace Framework\Session;

interface SessionInterface
{

    
    /**
     * Récupère une clé en session
     *
     * @param  string $key
     * @param  mixed $default
     * @return void
     */
    public function get(string $key, $default = null);
    
    /**
     * Définit une clé en session
     *
     * @param  string $key
     * @param  string $value
     * @return void
     */
    public function set(string $key, $value): void;
    
    /**
     * Supprime une clé en session
     *
     * @param  string $key
     * @return void
     */
    public function delete(string $key): void;
}
