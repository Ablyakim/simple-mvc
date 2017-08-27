<?php

namespace App\Model;

use Framework\Security\Model\UserProviderInterface;

/**
 * Class FromConfigUserProvider
 */
class FromConfigUserProvider implements UserProviderInterface
{
    /**
     * @var array
     */
    protected $usersData;

    /**
     * FromConfigUserProvider constructor.
     * @param $usersData
     */
    public function __construct($usersData)
    {
        $this->usersData = $usersData;
    }

    /**
     * @inheritDoc
     */
    public function getUserByLogin($login)
    {
        foreach ($this->usersData as $userData) {
            if ($userData['login'] == $login) {
                $user = new User();
                $user->setLogin($login)
                    ->setPassword($userData['password'])
                    ->setPasswordSalt($userData['password_salt']);

                return $user;
            }
        }

        return null;
    }
}