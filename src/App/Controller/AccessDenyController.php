<?php

namespace App\Controller;

use Framework\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class AccessDenyController
 */
class AccessDenyController extends AbstractController
{
    /**
     * @return RedirectResponse
     */
    public function executeAction()
    {
        return new RedirectResponse('/login');
    }
}