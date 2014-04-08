<?php
namespace Trolamine\Core\Authentication;

/**
 * The authentication object
 * 
 * @author Remi
 *
 */
interface Authentication {
    
    const ANONYMOUS = 0;
    const REMEMBER_ME = 1;
    const FULLY_AUTHENTICATED = 2;
    
    /**
     * Returns the password
     *
     * @return string
     */
    function getCredentials();
    
    /**
     * Returns the username
     *
     * @return string
     */
    function getPrincipal();
    
    /**
     * Returns the authenticated user object
     * 
     * @return mixed authenticated user
     */
    function getAuthenticatedUser();
    
    /**
     * Returns the authentication mode
     * 
     * @return string the authentication mode
     */
    function getAuthenticationMode();
    
    /**
     * Returns the authenticated user authorities list
     * 
     * @return array
     */
    function getAuthorities();
    
}
