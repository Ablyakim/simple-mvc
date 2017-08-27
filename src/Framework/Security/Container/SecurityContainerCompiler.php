<?php


namespace Framework\Security\Container;

use Framework\Di\CompilerInterface;
use Framework\EventNames;
use Framework\Security\EventListener\AccessDenyListener;
use Framework\Security\Model\AuthManager;
use Framework\Security\Util\PasswordEncoder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SecurityContainerCompiler
 */
class SecurityContainerCompiler implements CompilerInterface
{
    /**
     * @inheritDoc
     */
    public function compile(ContainerBuilder $container)
    {
        $this->registerExceptionListener($container);
        $this->registerAuthManager($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerExceptionListener(ContainerBuilder $container)
    {
        $container->register('on_access_deny_listener', AccessDenyListener::class)
            ->addArgument(new Reference('service_container'))
            ->addTag(
                'event_listener',
                ['event' => EventNames::EXCEPTION_EVENT, 'method' => 'onAccessDeny', 'priority' => -899]
            );
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerAuthManager(ContainerBuilder $container)
    {
        $container->register('default_password_encoder', PasswordEncoder::class);

        $container->register('auth_manager', AuthManager::class)
            ->addArgument(new Reference('user_provider'))
            ->addArgument(new Reference('session'))
            ->addArgument(new Reference('default_password_encoder'));
    }
}