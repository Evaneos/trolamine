<?php
namespace Trolamine\Core\Authentication\Password;

class MD5Encoder implements PasswordEncoder {
    
    /**
     * 
     * @param  string $password
     * @return string
     */
    function encodePassword($password) {
        return md5($password);
    }
    
}