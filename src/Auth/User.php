<?php

namespace App\Auth;

use DateTime;
use Framework\Auth\User as AuthUser;

class User implements AuthUser
{
    public $id;
    public $username;
    public $password;
    public $email;
    public $passwordReset;
    public $passwordResetAt;

    public function setPasswordResetAT($date)
    {
        if (is_string($date)) {
            $this->passwordResetAt = new DateTime($date);
        } else {
            $this->passwordResetAt = $date;
        }
    }

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

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of passwordReset
     */
    public function getPasswordReset()
    {
        return $this->passwordReset;
    }

    /**
     * Set the value of passwordReset
     *
     * @return  self
     */
    public function setPasswordReset($passwordReset)
    {
        $this->passwordReset = $passwordReset;

        return $this;
    }

    /**
     * Get the value of passwordResetAt
     */
    public function getPasswordResetAt(): ?DateTime
    {
        return $this->passwordResetAt;
    }
}
