<?php

namespace Framework\EventDispatcher;

/**
 * Class EventDispatcher
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var array
     */
    protected $sortedListeners = [];

    /**
     * @inheritDoc
     */
    public function dispatch($eventName, Event $event = null)
    {
        foreach ($this->getListeners($eventName) as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            call_user_func($listener, $event, $eventName, $this);
        }
    }

    /**
     * @inheritDoc
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sortedListeners[$eventName]);
    }

    /**
     * @inheritDoc
     */
    public function getListeners($eventName)
    {
        if (null !== $eventName) {
            if (!isset($this->listeners[$eventName])) {
                return [];
            }

            if (!isset($this->sortedListeners[$eventName])) {
                krsort($this->listeners[$eventName]);
                $this->sortedListeners[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
            }

            return $this->sortedListeners[$eventName];
        }

        return array_filter($this->sortedListeners);
    }
}