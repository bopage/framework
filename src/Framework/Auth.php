<?php

namespace Framework;

use Framework\Auth\User;

interface Auth
{

    
    /**
     * Renvoie un utilisateur
     *
     * @return User|null
     */
    public function getUser(): ?User;
}
