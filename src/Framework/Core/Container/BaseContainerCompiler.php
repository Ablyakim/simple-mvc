<?php

namespace Framework\Core\Container;

use Framework\Core\EventListener\ControllerListener;
use Framework\Di\CompilerInterface;
use Framework\EventDispatcher\EventDispatcher;
use Framework\EventNames;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BaseContainerCompiler implements CompilerInterface
{
    /**
     * @inheritDoc
     */
    public function compile(ContainerBuilder $container)
    {
        $container->register('event_dispatcher', EventDispatcher::class);

        $container->register('controller_listener', ControllerListener::class)
            ->addTag('event_listener', ['event' => EventNames::REQUEST_EVENT, 'method' => 'processRequest']);
    }
}