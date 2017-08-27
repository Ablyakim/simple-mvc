<?php


namespace App\Container;

use App\Model\FromConfigUserProvider;
use App\Repository\TaskRepository;
use Framework\Di\CompilerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ServicesCompiler implements CompilerInterface
{
    /**
     * @inheritDoc
     */
    public function compile(ContainerBuilder $container)
    {
        $container->register('task_repository', TaskRepository::class)
            ->addArgument(new Reference('db'));

        $container->register('user_provider', FromConfigUserProvider::class)
            ->addArgument('%users%');
    }
}