<?php

namespace App\Account;

use App\Auth\User as AuthUser;

class User extends AuthUser
{

    
    /**
     * firtname
     *
     * @var string
     */
    private $firtname;
    /**
     * lastname
     *
     * @var string
     */
    private $lastname;
    
    /**
     * role
     *
     * @var string
     */
    private $role;

    public function getRoles(): array
    {
        return [$this->role];
    }

    /**
     * Get firtname
     *
     * @return  string|null
     */
    public function getFirtname(): ?string
    {
        return $this->firtname;
    }

    /**
     * Set firtname
     *
     * @param  string  $firtname  firtname
     *
     * @return  self
     */
    public function setFirtname(string $firtname)
    {
        $this->firtname = $firtname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return  string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Set lastname
     *
     * @param  string  $lastname  lastname
     *
     * @return  self
     */
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get role
     *
     * @return  string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * Set role
     *
     * @param  string  $role  role
     *
     * @return  self
     */
    public function setRole(string $role)
    {
        $this->role = $role;

        return $this;
    }
}
