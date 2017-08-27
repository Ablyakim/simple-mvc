<?php

namespace Framework\Core\Container;

use Framework\Core\EventListener\NoRouteListener;
use Framework\Core\EventListener\RouterListener;
use Framework\Core\Route\RouterLoader;
use Framework\Db\ConnectionProxy;
use Framework\Db\Query\QueryBuilder;
use Framework\Di\CompilerInterface;
use Framework\EventDispatcher\EventDispatcher;
use Framework\EventNames;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Session\Session;

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
        $this->registerDbConnection($container);
        $this->registerExceptionListener($container);
        $this->registerSessionManager($container);
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
    protected function registerExceptionListener(ContainerBuilder $container)
    {
        $container->register('on_exception_listener', NoRouteListener::class)
            ->addArgument(new Reference('service_container'))
            ->addTag(
                'event_listener',
                ['event' => EventNames::EXCEPTION_EVENT, 'method' => 'onAppException', 'priority' => -999]
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
            ->addMethodCall('addGlobal', ['auth_manager', new Reference('auth_manager')]);
        //enable in production mode
//            ->addArgument(['cache' => $viewParams['cache_dir']]);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerDbConnection(ContainerBuilder $container)
    {
        $container->register('db', ConnectionProxy::class)
            ->addArgument('%db_config%');

        $container->register('query_builder', QueryBuilder::class)
            ->addArgument(new Reference('db'));
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerSessionManager(ContainerBuilder $container)
    {
        $container->register('session', Session::class);
    }
}