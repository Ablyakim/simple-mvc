<?php


namespace Framework\Security\Util;


use Framework\Security\Model\UserInterface;

class PasswordEncoder implements PasswordEncoderInterface
{
    /**
     * @inheritDoc
     */
    public function encode($password, UserInterface $user)
    {
        return md5(md5($password) . $user->getPasswordSalt());
    }
}