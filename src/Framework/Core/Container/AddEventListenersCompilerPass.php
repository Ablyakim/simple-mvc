<?php


namespace Framework\Core\Container;

use Framework\EventDispatcher\EventDispatcher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AddEventListenersCompilerPass
 */
class AddEventListenersCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $evenListenersIds = $container->findTaggedServiceIds('event_listener');

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $container->get('event_dispatcher');

        foreach ($evenListenersIds as $evenListenersId => $params) {
            $listenerInstance = $container->get($evenListenersId);
            $priority = isset($params[0]['priority']) ? $params[0]['priority'] : 0;

            $eventDispatcher->addListener(
                $params[0]['event'],
                [$listenerInstance, $params[0]['method']],
                $priority
            );
        }
    }
}