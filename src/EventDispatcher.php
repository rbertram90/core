<?php

namespace rbwebdesigns\core;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface {

    public function __construct(protected EventListenerProvider $listenerProvider) {}

    public function dispatch(object $event) {
        if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            return $event;
        }

        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
            $listener($event);
        }

        return $event;
    }

}
