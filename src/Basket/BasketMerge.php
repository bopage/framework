<?php
namespace App\Basket;

use App\Auth\Event\LoginEvent;
use App\Basket\Table\BasketTable;

class BasketMerge
{
    private $sessionBasket;
    private $basketTable;

    public function __construct(SessionBasket $sessionBasket, BasketTable $basketTable)
    {
        $this->basketTable = $basketTable;
        $this->sessionBasket = $sessionBasket;
    }

    public function __invoke(LoginEvent $event)
    {
        $user = $event->getTarget();
        (new DatabaseBasket($user->getId(), $this->basketTable))->merge($this->sessionBasket);
        $this->sessionBasket->empty();
    }
}
