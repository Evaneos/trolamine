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
     * @param boolean $accountNonExpired
     * @param boolean $accountNonLocked
     * @param boolean $credentialsNonExpired
     * @param boolean $enabled
     */
    public function __construct($user, $username, $password, $accountNonExpired=true, $accountNonLocked=true, $credentialsNonExpired=true, $enabled=true) {
        $this->user = $user;
        $this->username = $username;
        $this->password = $password;
        $this->accountNonExpired = $accountNonExpired;
        $this->accountNonLocked = $accountNonLocked;
        $this->credentialsNonExpired = $credentialsNonExpired;
        $this->enabled = $enabled;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::getUser()
     */
    function getUser() {
        return $this->user;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::getPassword()
     */
    function getPassword() {
        return $this->password;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::getSalt()
     */
    function getSalt() {
        return $this->salt;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::getUsername()
     */
    function getUsername() {
        return $this->username;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::isAccountNonExpired()
     */
    function isAccountNonExpired(){
        return $this->accountNonExpired;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::isAccountNonLocked()
     */
    function isAccountNonLocked(){
        return $this->accountNonLocked;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::isCredentialsNonExpired()
     */
    function isCredentialsNonExpired(){
        return $this->credentialsNonExpired;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\UserDetails::isEnabled()
     */
    function isEnabled(){
        return $this->enabled;
    }
}
