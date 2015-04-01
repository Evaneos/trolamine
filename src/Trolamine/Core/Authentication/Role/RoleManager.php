<?php
namespace Trolamine\Core\Authentication\Role;

use Trolamine\Core\Authentication\UserDetails;

interface RoleManager
{
    
    /**
     * 
     * @param UserDetails $userDetails
     * 
     * @return array the array of roles
     */
    function getRoles(UserDetails $userDetails);
}
