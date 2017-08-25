<?php

namespace Framework\Core\EventListener;

use Framework\Core\Event\RequestEvent;
use Framework\Core\Route\RouterLoader;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

/**
 * Class RouterListener
 */
class RouterListener
{
    /**
     * @var RouterLoader
     */
    protected $routerLoader;

    /**
     * @var Container
     */
    protected $container;

    /**
     * RouterListener constructor.
     * @param RouterLoader $routerLoader
     * @param Container $container
     */
    public function __construct(RouterLoader $routerLoader, Container $container)
    {
        $this->routerLoader = $routerLoader;
        $this->container = $container;
    }

    /**
     * @param RequestEvent $requestEvent
     */
    public function processRequest(RequestEvent $requestEvent)
    {
        $request = $requestEvent->getRequest();
        $routeCollection = $this->routerLoader->getRouteCollection();

        $context = new RequestContext();
        $context->fromRequest($requestEvent->getRequest());
        $matcher = new UrlMatcher($routeCollection, $context);
        try {
            $parameters = $matcher->matchRequest($request);
            $response = $this->callControllerByParams($parameters, $request);

            if (!$response instanceof Response) {
                throw new \LogicException('Controller should return Response instance');
            }

            $requestEvent->setResponse($response);
        } catch (ResourceNotFoundException $resourceNotFoundException) {

        } catch (MethodNotAllowedException $methodNotAllowedException) {

        }
    }

    /**
     * @param $parameters
     * @param $request
     *
     * @return mixed
     */
    protected function callControllerByParams($parameters, $request)
    {
        // move to some factory
        $controllerInstance = new $parameters['_controller']($this->container);

        return call_user_func_array(
            [$controllerInstance, sprintf('%sAction', $parameters['action'])],
            [$request]
        );
    }
}