<?php


namespace App\Controller;

use Framework\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PageNotFoundController
 */
class PageNotFoundController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function executeAction(Request $request)
    {
        return new Response('Page not found', 404);
    }
}