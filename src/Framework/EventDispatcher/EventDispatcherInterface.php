<?php

namespace Framework\EventDispatcher;

/**
 * Interface EventDispatcherInterface
 */
interface EventDispatcherInterface
{
    /**
     * @param $eventName
     * @param Event|null $event
     *
     * @return mixed
     */
    function dispatch($eventName, Event $event = null);

    /**
     * @param $eventName
     * @param $listener
     * @param int $priority
     *
     * @return mixed
     */
    function addListener($eventName, $listener, $priority = 0);

    /**
     * @param string $eventName
     *
     * @return array
     */
    public function getListeners($eventName);
}