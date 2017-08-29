<?php

namespace App\Container;

use App\Model\FromConfigUserProvider;
use App\Model\Uploader;
use App\Repository\ImageRepository;
use App\Repository\TaskRepository;
use App\Validator\TaskValidator;
use Framework\Di\CompilerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ServicesCompiler
 */
class ServicesCompiler implements CompilerInterface
{
    /**
     * @inheritDoc
     */
    public function compile(ContainerBuilder $container)
    {
        $container->register('task_repository', TaskRepository::class)
            ->addArgument(new Reference('db'));

        $container->register('image_repository', ImageRepository::class)
            ->addArgument(new Reference('db'));

        $container->register('user_provider', FromConfigUserProvider::class)
            ->addArgument('%users%');

        $container->register('uploader', Uploader::class)
            ->addArgument('%uploader_params%');

        $container->register('task_validator', TaskValidator::class)
            ->addArgument(new Reference('auth_manager'));
    }
}