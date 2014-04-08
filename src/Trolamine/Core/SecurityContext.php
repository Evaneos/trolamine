<?php
namespace Trolamine\Core;

use Trolamine\Core\Authentication\Authentication;
use Trolamine\Core\Access\AccessDecisionManager;

interface SecurityContext {
    
    /**
     * Authentication setter
     * 
     * @param Authentication $authentication
     */
    function setAuthentication(Authentication $authentication);
    
    /**
     * Authentication getter
     * 
     * @return Authentication
     */
    function getAuthentication();
    
    
    /**
     * AccessDecisionManager getter
     * 
     * @return AccessDecisionManager
     */
    function getAccessDecisionManager();
    
    /**
     * Quick access to the hasRole condition
     * 
     * @param string $roleName
     */
    function hasRole($roleName);
}
