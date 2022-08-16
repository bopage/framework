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
    private $firstname;
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
     * Get firstname
     *
     * @return  string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Set firstname
     *
     * @param  string|null  $firtname  firtname
     *
     * @return  self
     */
    public function setFirstname(?string $firstname)
    {
        $this->firstname = $firstname;

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
     * @param  string|null  $lastname  lastname
     *
     * @return  self
     */
    public function setLastname(?string $lastname)
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
