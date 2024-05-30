<?php

namespace RWFramework\Framework\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface {

    private iterable $listeners = [];

    public function dispatch(object $event): object {
        // Loop over listeners
        foreach($this->getListenersForEvent($event) as $listener) {
            // Break if event is stopped
            if($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return $event;
            }
            
            $listener($event);
        }

        return $event;
    }

    // $eventName for example, ResponseEvent::class (Can be more than one)
    public function addListener(string $eventName, callable $listener): self {
        $this->listeners[$eventName][] = $listener;

        return $this;
    }

    /** PSR-14 implementation
     * @param object $event
     *   An event for which to return the relevant listeners.
     * @return iterable<callable>
     *   An iterable (array, iterator, or generator) of callables.  Each
     *   callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent(object $event) : iterable
    {
        $eventName = get_class($event);

        if (array_key_exists($eventName, $this->listeners)) {
            return $this->listeners[$eventName];
        }

        return [];
    }
}