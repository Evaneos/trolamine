<?php
namespace Trolamine\Core\Authentication\Password;

interface PasswordEncoder
{
    
    /**
     * Encodes the given password
     * 
     * @param  string $password the unencoded password
     * 
     * @return string the encoded password
     */
    function encodePassword($password);
}
