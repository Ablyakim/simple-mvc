<?php

namespace Framework\Di;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Interface CompilerInterface
 * @package Framework\Di
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
    public function compile(ContainerBuilder $container);
}