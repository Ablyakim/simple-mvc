<?php

namespace Framework\Core\Container;

use Framework\Core\EventListener\RouterListener;
use Framework\Core\Route\RouterLoader;
use Framework\Di\CompilerInterface;
use Framework\EventDispatcher\EventDispatcher;
use Framework\EventNames;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class BaseContainerCompiler
 */
class BaseContainerCompiler implements CompilerInterface
{
    /**
     * @inheritDoc
     */
    public function compile(ContainerBuilder $container)
    {
        $container->register('event_dispatcher', EventDispatcher::class);

        $container->register('route_loader', RouterLoader::class)
            ->addArgument('%routes_path%');

        $container->register('controller_listener', RouterListener::class)
            ->addArgument(new Reference('route_loader'))
            ->addArgument(new Reference('service_container'))
            ->addTag('event_listener', ['event' => EventNames::REQUEST_EVENT, 'method' => 'processRequest']);
    }
}