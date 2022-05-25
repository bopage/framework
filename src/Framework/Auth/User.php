<?php

namespace Framework\Auth;

interface User
{

    
    /**
     * L'identifiant de l'utilisateur
     *
     * @return string
     */
    public function getUsername(): string;
    
    /**
     * Les Rôles de l'utilisateur
     *
     * @return string[]
     */
    public function getRoles(): array;
}
