<?php


namespace Framework\Core\Controller;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;

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
     * @param $template
     * @param array $context
     *
     * @return Response
     */
    protected function render($template, $context = [])
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig.env');
        $template = $twig->render('index/index.html.twig');

        return new Response($template);
    }
}