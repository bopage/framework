<?php

namespace App\Auth;

use App\Auth\User as AuthUser;
use Framework\Auth;
use Framework\Auth\User;
use Framework\Database\NoRecordException;
use Framework\Session\SessionInterface;

class DatabaseAuth implements Auth
{
    private $userTable;

    private $session;

    private $user;

    public function __construct(UserTable $userTable, SessionInterface $session)
    {
        $this->userTable = $userTable;
        $this->session = $session;
    }
    
    /**
     * Vérifie si on a un utilisateur valide
     *
     * @param  string $username
     * @param  string $password
     * @return AuthUser|null
     */
    public function login(string $username, string $password): ?AuthUser
    {
        if (empty($username) || empty($password)) {
            return null;
        }
        /** @var AuthUser */
        $user = $this->userTable->findBy('username', $username);
        if ($user && password_verify($password, $user->getPassword())) {
            $this->setUser($user);
            return $user;
        }
        return null;
    }

    public function logout(): void
    {
        $this->session->delete('auth.user');
    }
    
    /**
     * Récupère un utilisateur
     *
     * @return User
     */
    public function getUser(): ?User
    {
        if ($this->user) {
            return $this->user;
        }
        $userId = $this->session->get('auth.user');
        if ($userId) {
            try {
                $this->user = $this->userTable->find($userId);
                return $this->user;
            } catch (NoRecordException $exception) {
                $this->session->delete('auth.user');
                return null;
            }
        }
        return null;
    }
    
    /**
     * Insert un utilisateur
     *
     * @return void
     */
    public function setUser(AuthUser $user): void
    {
        $this->session->set('auth.user', $user->getId());
        $this->user = $user;
    }
}
