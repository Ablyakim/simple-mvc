<?php

namespace Framework\Core\Controller;

use Framework\Di\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait ControllerCallerTrait
 */
trait ControllerCallerTrait
{
    /**
     * @param $parameters
     * @param $request
     *
     * @return Response
     */
    protected function callControllerByParams($parameters, $request)
    {
        if (!$this instanceof ContainerAwareInterface) {
            throw new \LogicException(
                sprintf('Class %s should be instance of %s', get_class($this), ContainerAwareInterface::class)
            );
        }

        /** @var $this ContainerAwareInterface */
        $controllerInstance = new $parameters['_controller']($this->getContainer());

        return call_user_func_array(
            [$controllerInstance, sprintf('%sAction', $parameters['action'])],
            [$request]
        );
    }
}