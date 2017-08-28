<?php

namespace Framework\Core\Controller;

use Framework\Security\Exception\AccessDenyException;
use Framework\Security\Model\AuthManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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

    /**
     * @return AuthManager
     */
    protected function getAuthManager()
    {
        return $this->container->get('auth_manager');
    }

    /**
     * @return SessionInterface
     */
    protected function getSession()
    {
        return $this->container->get('session');
    }

    /**
     * @throws AccessDenyException
     */
    protected function checkAccess()
    {
        $this->getAuthManager()->authenticate();
    }

    /**
     * @param $template
     * @param array $context
     *
     * @return Response
     */
    protected function render($template, $context = [])
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig.env');
        $template = $twig->render($template, $context);

        return new Response($template);
    }
}