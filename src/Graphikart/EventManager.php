<?php

namespace Graphikart;

use Psr\EventManager\EventInterface;
use Psr\EventManager\EventManagerInterface;

class EventManager implements EventManagerInterface
{

    
    /**
     * listeners
     *
     * @var array
     */
    private $listeners = [];


    public function attach(string $event, callable $callback, int $priority = 0): bool
    {
        $this->listeners[$event][] = [
            'callback' => $callback,
            'priority' => $priority
        ];

        return true;
    }

    public function detach(string $event, callable $callback): bool
    {
        $this->listeners[$event] = array_filter(
            $this->listeners[$event],
            function ($listeners) use ($callback) {
                return $listeners['callback'] !== $callback;
            }
        );
        return true;
    }

    public function clearListeners(string $event): void
    {
        $this->listeners[$event] = [];
    }

    public function trigger($event, $target = null, array $params = [])
    {
        if (is_string($event)) {
            $event = $this->makeEvent($event, $target, $params);
        }

        if (isset($this->listeners[$event->getName()])) {
            $listeners = $this->listeners[$event->getName()];
            usort($listeners, function ($listenersA, $listenersB) {
                return $listenersB['priority'] - $listenersA['priority'];
            });

            foreach ($listeners as ['callback' => $callback]) {
                if ($event->isPropagationStopped()) {
                    break;
                }
                call_user_func($callback, $event);
            }
        }
    }

    private function makeEvent(string $eventName, $target = null, array $params = []): EventInterface
    {
        $event = new Event;
        $event->setName($eventName);
        $event->setTarget($target);
        $event->setParams($params);
        
        return $event;
    }
}
