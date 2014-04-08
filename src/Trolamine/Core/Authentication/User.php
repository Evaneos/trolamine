<?php
namespace Trolamine\Core\Authentication;

/**
 * A base user implementing UserDetails
 */
class User implements UserDetails {
    
    private $username;
    private $password;
    private $accountNonExpired;
    private $accountNonLocked;
    private $credentialsNonExpired;
    private $enabled;
    
    /**
     * Constructor
     * 
     * @param string  $username
     * @param string  $password
     * @param boolean $accountNonExpired
     * @param boolean $accountNonLocked
     * @param boolean $credentialsNonExpired
     * @param boolean $enabled
     */
    public function __construct($username, $password, $accountNonExpired=true, $accountNonLocked=true, $credentialsNonExpired=true, $enabled=true) {
        $this->username = $username;
        $this->password = $password;
        $this->accountNonExpired = $accountNonExpired;
        $this->accountNonLocked = $accountNonLocked;
        $this->credentialsNonExpired = $credentialsNonExpired;
        $this->enabled = $enabled;
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
