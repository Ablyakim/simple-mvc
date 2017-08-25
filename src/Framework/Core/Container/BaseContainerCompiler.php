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
        $this->registerEventListener($container);
        $this->registerRouterLoader($container);
        $this->registerViewComponent($container);
        $this->registerControllerListener($container);

    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerControllerListener(ContainerBuilder $container)
    {
        $container->register('controller_listener', RouterListener::class)
            ->addArgument(new Reference('route_loader'))
            ->addArgument(new Reference('service_container'))
            ->addTag(
                'event_listener',
                ['event' => EventNames::REQUEST_EVENT, 'method' => 'processRequest']
            );
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerEventListener(ContainerBuilder $container)
    {
        $container->register('event_dispatcher', EventDispatcher::class);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerRouterLoader(ContainerBuilder $container)
    {
        $container->register('route_loader', RouterLoader::class)
            ->addArgument('%routes_path%');
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerViewComponent(ContainerBuilder $container)
    {
        $viewParams = $container->getParameter('view');

        $container->register('twig.loader_file_system', \Twig_Loader_Filesystem::class)
            ->addArgument($viewParams['template_dir']);

        $container->register('twig.env', \Twig_Environment::class)
            ->addArgument(new Reference('twig.loader_file_system'))
            ->addArgument(['cache' => $viewParams['cache_dir']]);
    }
}