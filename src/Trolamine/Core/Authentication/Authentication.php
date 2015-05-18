<?php
namespace Trolamine\Core\Authentication;

/**
 * The authentication object
 *
 * @author Remi
 *
 */
interface Authentication
{
    
    const ANONYMOUS = 0;
    const REMEMBER_ME = 1;
    const FULLY_AUTHENTICATED = 2;
    
    /**
     * Returns the password
     *
     * @return string
     */
    public function getCredentials();
    
    /**
     * Returns the username
     *
     * @return string
     */
    public function getPrincipal();
    
    /**
     * Returns the authenticated user object
     *
     * @return UserDetails authenticated user
     */
    public function getAuthenticatedUser();
    
    /**
     * Returns the authentication mode
     *
     * @return string the authentication mode
     */
    public function getAuthenticationMode();
    
    /**
     * Returns the authenticated user authorities list
     *
     * @return array
     */
    public function getAuthorities();
}
