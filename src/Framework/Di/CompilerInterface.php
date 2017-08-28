<?php

namespace Framework\Di;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Interface CompilerInterface
 */
interface CompilerInterface
{
    /**
     * Do something with container
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    function compile(ContainerBuilder $container);
}