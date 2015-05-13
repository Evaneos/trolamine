<?php
namespace Trolamine\Core\Authentication;

/**
 * A base user implementing UserDetails
 */
class User implements UserDetails
{

    private $user;
    private $username;
    private $password;
    private $salt;
    private $accountNonExpired;
    private $accountNonLocked;
    private $credentialsNonExpired;
    private $enabled;

    /**
     * Constructor
     *
     * @param string  $user
     * @param string  $username
     * @param string  $password
     * @param string  $salt
     * @param boolean $accountNonExpired
     * @param boolean $accountNonLocked
     * @param boolean $credentialsNonExpired
     * @param boolean $enabled
     */
    public function __construct($user, $username, $password, $salt = null, $accountNonExpired=true, $accountNonLocked=true, $credentialsNonExpired=true, $enabled=true)
    {
        $this->user = $user;
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->accountNonExpired = $accountNonExpired;
        $this->accountNonLocked = $accountNonLocked;
        $this->credentialsNonExpired = $credentialsNonExpired;
        $this->enabled = $enabled;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::getUser()
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::getPassword()
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::getSalt()
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::getUsername()
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::isAccountNonExpired()
     */
    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::isAccountNonLocked()
     */
    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::isCredentialsNonExpired()
     */
    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::isEnabled()
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}
