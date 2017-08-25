<?php


namespace Framework\Core\Route;

use Symfony\Component\Routing\RouteCollection;

class RouterLoader
{
    /**
     * @var string
     */
    protected $source;

    /**
     * @var RouteCollection
     */
    protected $routeCollection;

    /**
     * RouterLoader constructor.
     * @param string $source
     */
    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * @return RouteCollection
     */
    public function getRouteCollection()
    {
        if(!$this->routeCollection) {
            $this->routeCollection = $this->loadRouteCollection();
        }

        return $this->routeCollection;
    }

    /**
     * @return RouteCollection
     */
    protected function loadRouteCollection()
    {
        if(!file_exists($this->source)) {
            throw new \LogicException(sprintf('File: %s is not existing'));
        }

        return include $this->source;
    }
}