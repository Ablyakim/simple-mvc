<?php

namespace Framework\Security\Util;

use Framework\Security\Model\UserInterface;

/**
 * Interface PasswordEncoderInterface
 */
interface PasswordEncoderInterface
{
    /**
     * @param string $password not encoded user password
     * @param UserInterface $user
     *
     * @return string
     */
    function encode($password, UserInterface $user);
}