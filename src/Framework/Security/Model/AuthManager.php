<?php


namespace Framework\Security\Model;

use Framework\Security\Exception\AccessDenyException;
use Framework\Security\Exception\UserNotAuthenticatedException;
use Framework\Security\Exception\UserNotFoundException;
use Framework\Security\Exception\WrongPasswordException;
use Framework\Security\Util\PasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthManager
{
    const LOGIN_IN_SESSION_KEY = 'security/login';

    /**
     * @var UserProviderInterface
     */
    protected $userProvider;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var PasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * AuthManager constructor.
     * @param UserProviderInterface $userProvider
     * @param SessionInterface $session
     * @param PasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        UserProviderInterface $userProvider,
        SessionInterface $session,
        PasswordEncoderInterface $passwordEncoder
    ) {
        $this->userProvider = $userProvider;
        $this->session = $session;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @return UserInterface
     *
     * @throws AccessDenyException
     */
    public function getAuthenticatedUser()
    {
        $this->authenticate();

        return $this->user;
    }

    /**
     * @throws AccessDenyException
     */
    public function authenticate()
    {
        try {
            $login = $this->session->get(static::LOGIN_IN_SESSION_KEY);

            if (!$login) {
                throw new UserNotAuthenticatedException();
            }

            $user = $this->userProvider->getUserByLogin($login);

            if (!$user) {
                throw new UserNotFoundException();
            }

            $this->user = $user;
        } catch (\Exception $e) {
            throw AccessDenyException::createFromOriginalException($e);
        }
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        try {
            $this->authenticate();

            return true;
        } catch (AccessDenyException $e) {
            return false;
        }
    }

    /**
     * @param string $login
     * @param string $password
     *
     * @throws AccessDenyException
     */
    public function login($login, $password)
    {
        try {
            $user = $this->userProvider->getUserByLogin($login);

            if (!$user) {
                throw new UserNotFoundException();
            }

            if ($user->getPassword() !== $this->passwordEncoder->encode($password, $user)) {
                throw new WrongPasswordException();
            }

            $this->onSuccessLogin($user);
        } catch (\Exception $e) {
            throw AccessDenyException::createFromOriginalException($e);
        }
    }

    /**
     * @return void
     */
    public function logout()
    {
        $this->session->clear();
    }

    /**
     * @param UserInterface $user
     */
    protected function onSuccessLogin(UserInterface $user)
    {
        $this->session->set(static::LOGIN_IN_SESSION_KEY, $user->getLogin());
    }
}