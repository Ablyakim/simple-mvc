<?php

namespace Framework\Di;

use Symfony\Component\DependencyInjection\Container;

/**
 * Interface ContainerAwareInterface
 * @package Framework\Di
 */
interface ContainerAwareInterface
{
    /**
     * @return Container
     */
    function getContainer();
}