<?php


namespace App\Controller;

use Framework\Core\Controller\AbstractController;
use Framework\Security\Exception\AccessDenyException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        return $this->render('security/login.html.twig', [
            'login' => $this->getLastUserLogin(),
            'error' => $this->getLastAuthenticationError()
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws AccessDenyException
     */
    public function postLoginAction(Request $request)
    {
        $login = $request->get('login');
        $password = $request->get('password');

        try {
            $this->getAuthManager()->login($login, $password);
        } catch (AccessDenyException $e) {
            $this->getSession()->set('auth/last-error', 'Your login or password do not match.');
            $this->getSession()->set('auth/last-login', $login);
            throw $e;
        }

        return new RedirectResponse('/');
    }

    /**
     * @return mixed
     */
    protected function getLastUserLogin()
    {
        $login = $this->getSession()->get('auth/last-login');
        $this->getSession()->remove('auth/last-login');

        return $login;
    }

    /**
     * @return mixed
     */
    protected function getLastAuthenticationError()
    {
        $error = $this->getSession()->get('auth/last-error');
        $this->getSession()->remove('auth/last-error');

        return $error;
    }
}