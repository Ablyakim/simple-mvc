<?php

namespace Framework;

use Framework\Core\Event\ExceptionEvent;
use Framework\Core\Event\RequestEvent;
use Framework\Di\CompilerInterface;
use Framework\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AppAbstract
 */
abstract class AppAbstract
{
    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * Retrieve config file path
     *
     * @return string
     */
    abstract public function getConfigFilePath();

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function processRequest(Request $request)
    {
        $this->initializeContainer();

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->container->get('event_dispatcher');
        try {
            $requestEvent = new RequestEvent($request);
            //@see Framework\Core\EventListener\RouterListener::processRequest
            $dispatcher->dispatch(EventNames::REQUEST_EVENT, $requestEvent);
            return $requestEvent->getResponse();
        } catch (\Exception $e) {
            return $this->handleException($request, $e);
        }
    }

    /**
     * @return array
     */
    protected function getConfiguration()
    {
        if (!$this->configuration) {
            $this->configuration = require $this->getConfigFilePath();;
        }

        return $this->configuration;
    }

    /**
     * @return void
     */
    protected function initializeContainer()
    {
        $config = $this->getConfiguration();

        $this->container = new ContainerBuilder();

        $this->addParamsToContainer();

        foreach ($config['container_compilers'] as $compilerClass) {
            $compiler = new $compilerClass;

            if (!$compiler instanceof CompilerInterface) {
                throw new \LogicException('Compiler should be an instance of "Framework\Di\CompilerInterface"');
            }

            $compiler->compile($this->container);
        }

        foreach ($config['compiler_passes'] as $compilerPass) {
            $this->container->addCompilerPass(new $compilerPass);
        }

        $this->container->compile();
    }

    /**
     * @param Request $request
     * @param \Exception $e
     *
     * @return Response
     *
     * @throws \Exception
     */
    protected function handleException(Request $request, \Exception $e)
    {
        $event = new ExceptionEvent($request, $e);

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->container->get('event_dispatcher');

        $dispatcher->dispatch(EventNames::EXCEPTION_EVENT, $event);

        if (!$event->getResponse()) {
            //it is should filter exception in production mode!
            throw $e;
        }

        return $event->getResponse();
    }

    /**
     * @return void
     */
    protected function addParamsToContainer()
    {
        $config = $this->getConfiguration();

        $this->container->setParameter('db_config', $config['db']);
        $this->container->setParameter('routes_path', $config['routes']);
        $this->container->setParameter('view', $config['view']);
        $this->container->setParameter('error_controllers', $config['error_controllers']);
        $this->container->setParameter('users', $config['users']);
        $this->container->setParameter('uploader_params', $config['uploader']);
    }
}