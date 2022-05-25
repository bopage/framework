<?php

namespace App\Auth;

use Framework\Auth\User as AuthUser;

class User implements AuthUser
{
    public $id;
    public $username;
    public $password;
    public $email;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return [];
    }

    /**
     * Get the value of password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
}
