<?php
namespace Trolamine\Core\Operation;

use \Trolamine\Core\Permission\Permission;
use \Trolamine\Core\Permission\PermissionEvaluator;
use \Trolamine\Core\Authentication\Authentication;

class SecurityExpressionRoot extends AbstractOperation implements SecurityExpressionOperations
{
    
    /**
     * The permission evaluator
     *
     * @var PermissionEvaluator
     */
    private $permissionEvaluator;
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::denyAccess()
     */
    public function denyAccess()
    {
        return false;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::hasAuthority()
     */
    public function hasAuthority($authority)
    {
        $authoritySet = $this->getAuthoritySet();
        return in_array($authority, $authoritySet);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::hasAnyAuthority()
     */
    public function hasAnyAuthority($authorities)
    {
        $authoritySet = $this->getAuthoritySet();
        foreach ($authorities as $authority) {
            if (in_array($authority, $authoritySet)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::hasRole()
     */
    public function hasRole($role)
    {
        return $this->hasAuthority($role);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::hasAnyRole()
     */
    public function hasAnyRole($roles)
    {
        return $this->hasAnyAuthority($roles);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::isAnonymous()
     */
    public function isAnonymous()
    {
        return (
            $this->getAuthentication()->getAuthenticationMode() == Authentication::ANONYMOUS ||
            !$this->isAuthenticated()
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::isAuthenticated()
     */
    public function isAuthenticated()
    {
        return ($this->isRememberMe() || $this->isFullyAuthenticated());
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::isRememberMe()
     */
    public function isRememberMe()
    {
        return $this->getAuthentication()->getAuthenticationMode() == Authentication::REMEMBER_ME;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::isFullyAuthenticated()
     */
    public function isFullyAuthenticated()
    {
        return $this->getAuthentication()->getAuthenticationMode() == Authentication::FULLY_AUTHENTICATED;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::hasPermission()
     */
    public function hasPermission($target, Permission $permission)
    {
        $this->permissionEvaluator->hasPermission($this->authentication, $target, $permission);
    }
    
    /**
     * @return array<string> the list of authorities detained by the authenticated user
     */
    protected function getAuthoritySet()
    {
        $authorities =  $this->getAuthentication()->getAuthorities();
        
        if ($authorities == null) {
            $authorities = array();
        }
        
        return $authorities;
    }
}
