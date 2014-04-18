<?php
namespace Trolamine\Core\Permission;

use Trolamine\Core\Authentication\Authentication;

interface PermissionEvaluator
{
    
    /**
     * Evaluates the permission for the authenticated user on the object passed in parameter
     * 
     * @param  Authentication authentication      represents the user in question. Should not be null.
     * @param  object         targetDomainObject  the domain object for which permissions should be checked. May be null
     *                                            in which case implementations should return false, as the null condition
     *                                            can be checked explicitly in the expression.
     * @param  Permission     permission          a representation of the permission object as supplied by the expression system. Not null.
     * 
     * @return boolean        true if the permission is granted, false otherwise
     */
    function hasPermission(Authentication $authentication, $targetDomainObject, Permission $permission);
}
