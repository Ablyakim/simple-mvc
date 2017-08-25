<?php

namespace Framework;

use Framework\Core\Container\AddEventListenersCompiler;
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

        $requestEvent = new RequestEvent($request);

        $dispatcher->dispatch(EventNames::REQUEST_EVENT, $requestEvent);

        return $requestEvent->getResponse();
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

        $this->container->setParameter('db_config', $config['db']);

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
}