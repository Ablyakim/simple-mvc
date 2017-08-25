<?php

namespace Framework\Core\EventListener;

use Framework\Core\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ControllerListener
 */
class ControllerListener
{
    public function processRequest(RequestEvent $requestEvent)
    {
        $response = new Response();
        $response->setContent('Hello world');

        $requestEvent->setResponse($response);
    }
}