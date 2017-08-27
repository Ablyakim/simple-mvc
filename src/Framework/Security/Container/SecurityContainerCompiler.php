<?php


namespace Framework\Security\Container;

use Framework\Di\CompilerInterface;
use Framework\EventNames;
use Framework\Security\EventListener\AccessDenyListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SecurityContainerCompiler implements CompilerInterface
{
    /**
     * @inheritDoc
     */
    public function compile(ContainerBuilder $container)
    {
        $this->registerExceptionListener($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerExceptionListener(ContainerBuilder $container)
    {
        $container->register('exception_listener', AccessDenyListener::class)
            ->addArgument(new Reference('service_container'))
            ->addTag(
                'event_listener',
                ['event' => EventNames::EXCEPTION_EVENT, 'method' => 'onAccessDeny', 'priority' => -899]
            );
    }

}