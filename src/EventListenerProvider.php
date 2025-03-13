<?php

namespace rbwebdesigns\core;

use Psr\EventDispatcher\ListenerProviderInterface;

class EventListenerProvider implements ListenerProviderInterface {

    /**
     * Nested array of listener objects, keyed by class name.
     * @var array<string,array>
     */
    protected array $listeners = [];

    public function getListenersForEvent(object $event): iterable {
        $eventType = get_class($event);

        if (array_key_exists($eventType, $this->listeners)) {
            return $this->listeners[$eventType];
        }

        return [];
    }

    public function addListener(string $eventType, callable $callable): self {
        $this->listeners[$eventType][] = $callable;

        return $this;
    }

    public function clearListeners(string $eventType): self {
        unset($this->listeners[$eventType]);

        return $this;
    }

}