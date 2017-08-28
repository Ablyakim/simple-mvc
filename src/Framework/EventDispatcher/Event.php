<?php

namespace Framework\EventDispatcher;

class Event
{
    /**
     * @var bool
     */
    private $propagationStopped = false;

    /**
     * @return bool
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }
}