<?php
namespace App\Auth\Event;

use App\Auth\User;
use Graphikart\Event;

class LoginEvent extends Event
{
    

    public function __construct(User $user)
    {
        $this->setName("auth.login");
        $this->setTarget($user);
    }

    public function getTarget(): User
    {
        return parent::getTarget();
    }
}
