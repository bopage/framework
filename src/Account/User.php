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
     * Get firtname
     *
     * @return  string
     */
    public function getFirtname(): string
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
     * @return  string
     */
    public function getLastname(): string
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
}
