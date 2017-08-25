<?php


namespace App\Controller;

use Framework\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class IndexController
 */
class IndexController extends AbstractController
{
    public function indexAction()
    {
        return new Response('Hello world');
    }
}