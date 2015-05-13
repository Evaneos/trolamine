<?php
namespace Trolamine\Core\Authentication\Password;

class MD5Encoder implements PasswordEncoder
{

    /**
     *
     * @param  string $password
     * @return string
     */
    public function encodePassword($password, $salt = null)
    {
        return md5($password);
    }
}
