<?php


namespace Framework\Core\Controller;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class AbstractController
 */
class AbstractController
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * AbstractController constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}