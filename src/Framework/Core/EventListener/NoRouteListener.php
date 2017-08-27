<?php

namespace Framework\Core\EventListener;

use Framework\Core\Controller\ControllerCallerTrait;
use Framework\Core\Event\ExceptionEvent;
use Framework\Core\Exception\NoRouteException;
use Framework\Di\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ExceptionListener
 */
class NoRouteListener implements ContainerAwareInterface
{
    use ControllerCallerTrait;
    
    /**
     * @var Container
     */
    protected $container;

    /**
     * NoRouteListener constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ExceptionEvent $exceptionEvent
     */
    public function onAppException(ExceptionEvent $exceptionEvent)
    {
        if ($exceptionEvent->getResponse() || !$exceptionEvent->getException() instanceof NoRouteException) {
            return;
        }

        $controllers = $this->container->getParameter('error_controllers');
        $request = $exceptionEvent->getRequest();
        $request->attributes->set('exception', $exceptionEvent);

        $response = $this->callControllerByParams($controllers['page_not_found'], $request);

        $exceptionEvent->setResponse($response);
    }
}