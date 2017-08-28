<?php

namespace Framework\Security\EventListener;

use Framework\Core\Controller\ControllerCallerTrait;
use Framework\Core\Event\ExceptionEvent;
use Framework\Di\ContainerAwareInterface;
use Framework\Security\Exception\AccessDenyException;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class AccessDenyListener
 */
class AccessDenyListener implements ContainerAwareInterface
{
    use ControllerCallerTrait;

    /**
     * @var Container
     */
    protected $container;

    /**
     * AccessDenyListener constructor.
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
    public function onAccessDeny(ExceptionEvent $exceptionEvent)
    {
        if ($exceptionEvent->getResponse() || !$exceptionEvent->getException() instanceof AccessDenyException) {
            return;
        }

        $controllers = $this->container->getParameter('error_controllers');

        $request = $exceptionEvent->getRequest();
        $request->attributes->set('exception', $exceptionEvent);

        $response = $this->callControllerByParams($controllers['access_deny'], $request);

        $exceptionEvent->setResponse($response);
    }
}