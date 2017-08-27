<?php


namespace Framework\Di;

use Symfony\Component\DependencyInjection\Container;

interface ContainerAwareInterface
{
    /**
     * @return Container
     */
    function getContainer();
}