<?php

namespace Framework\Security\Model;

/**
 * Interface UserProviderInterface
 */
interface UserProviderInterface
{
    /**
     * @param string $login
     *
     * @return UserInterface|null
     */
    public function getUserByLogin($login);
}