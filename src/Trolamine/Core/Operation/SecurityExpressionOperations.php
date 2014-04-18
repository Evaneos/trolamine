<?php
namespace Trolamine\Core\Operation;

use Trolamine\Core\Permission\Permission;
use Trolamine\Core\Authentication\Authentication;

/**
 * Describes the authorized security operations
 * 
 * @author Remi
 *
 */
interface SecurityExpressionOperations extends Operation
{
    
    /**
     * Determines if the {@link #getAuthentication()} has a particular authority within {@link Authentication#getAuthorities()}. This is a synonym for {@link #hasAuthority(String)}.
     * 
     * @param  string  authority the authority to test (i.e. "ROLE_USER")
     * 
     * @return boolean true if the authority is found, else false
     */
    function  hasAuthority($authority);
    
    /**
     * Determines if the {@link #getAuthentication()} has any of the specified authorities within {@link Authentication#getAuthorities()}. This is a synonym for {@link #hasAnyRole(String...)}.
     * 
     * @param  array<string> authorities the authorities to test (i.e. "ROLE_USER", "ROLE_ADMIN")
     * 
     * @return boolean       true if any of the authorities is found, else false
     */
    function hasAnyAuthority($authorities);
    
    /**
     * Determines if the {@link #getAuthentication()} has a particular authority within {@link Authentication#getAuthorities()}. This is a synonym for {@link #hasAuthority(String)}.
     * 
     * @param  string  authority the authority to test (i.e. "ROLE_USER")
     * 
     * @return boolean true if the authority is found, else false
     */
    function hasRole($role);
    
    /**
     * Determines if the {@link #getAuthentication()} has any of the specified authorities within {@link Authentication#getAuthorities()}. This is a synonym for {@link #hasAnyAuthority(String...)}.
     * 
     * @param  array<string> authorities the authorities to test (i.e. "ROLE_USER", "ROLE_ADMIN")
     * 
     * @return boolean       true if any of the authorities is found, else false
     */
    function hasAnyRole($roles);
    
    /**
     * Determines if the {@link #getAuthentication()} is anonymous
     * 
     * @return boolean true if the user is anonymous, else false
     */
    function isAnonymous();
    
    /**
     * Determines ifthe {@link #getAuthentication()} is authenticated
     * 
     * @return boolean true if the {@link #getAuthentication()} is authenticated, else false
    */
    function isAuthenticated();
    
    /**
     * Determines if the {@link #getAuthentication()} was authenticated using remember me
     * 
     * @return boolean true if the {@link #getAuthentication()} authenticated using remember me, else false
     */
    function isRememberMe();
    
    /**
     * Determines if the {@link #getAuthentication()} authenticated without the use of remember me
     * 
     * @return boolean true if the {@link #getAuthentication()} authenticated without the use of remember me, else false
     */
    function isFullyAuthenticated();
    
    /**
     * Determines if the {@link #getAuthentication()} has permission to access the target given the permission
     * 
     * @param  object     target     the target domain object to check permission on
     * @param  Permission permission the permission to check on the domain object (i.e. "read", "write", etc).
     * 
     * @return boolean    true if permission is granted to the {@link #getAuthentication()}, else false
     */
    function hasPermission($target, Permission $permission);
}
