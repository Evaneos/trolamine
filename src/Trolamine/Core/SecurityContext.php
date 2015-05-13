<?php
namespace Trolamine\Core;

use Trolamine\Core\Authentication\Authentication;
use Trolamine\Core\Access\AccessDecisionManager;

interface SecurityContext
{
    
    /**
     * Authentication setter
     *
     * @param Authentication $authentication
     */
    public function setAuthentication(Authentication $authentication);
    
    /**
     * Authentication getter
     *
     * @return Authentication
     */
    public function getAuthentication();
    
    
    /**
     * AccessDecisionManager getter
     *
     * @return AccessDecisionManager
     */
    public function getAccessDecisionManager();
    
    /**
     * Quick access to the hasRole condition
     *
     * @param string $roleName
     */
    public function hasRole($roleName);

    /**
     * @param array $roles
     */
    public function hasAnyRole($roles);
}
