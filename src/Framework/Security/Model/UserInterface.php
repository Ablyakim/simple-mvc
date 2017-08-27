<?php


namespace Framework\Security\Model;

/**
 * Interface UserInterface
 */
interface UserInterface
{
    /**
     * @param $login
     *
     * @return UserInterface
     */
    function setLogin($login);

    /**
     * @return string
     */
    function getLogin();

    /**
     * @param string $password
     * @return UserInterface
     */
    function setPassword($password);

    /**
     * @return string
     */
    function getPassword();

    /**
     * @param string $passwordSalt
     *
     * @return UserInterface
     */
    function setPasswordSalt($passwordSalt);

    /**
     * @return string
     */
    function getPasswordSalt();
}