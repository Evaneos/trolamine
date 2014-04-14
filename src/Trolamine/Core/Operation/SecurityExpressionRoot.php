<?php
namespace Trolamine\Core\Operation;

use \Trolamine\Core\Permission\Permission;
use \Trolamine\Core\Permission\PermissionEvaluator;
use \Trolamine\Core\Authentication\Authentication;

class SecurityExpressionRoot extends AbstractOperation implements SecurityExpressionOperations{
    
    /**
     * The permission evaluator
     * 
     * @var PermissionEvaluator
     */
    private $permissionEvaluator;
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::hasAuthority()
     */
    function  hasAuthority($authority){
        $authoritySet = $this->getAuthoritySet();
        return in_array($authority, $authoritySet);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::hasAnyAuthority()
     */
    function hasAnyAuthority($authorities){
        $authoritySet = $this->getAuthoritySet();
        foreach($authorities as $authority) {
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
    function hasRole($role){
        return $this->hasAuthority($role);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::hasAnyRole()
     */
    function hasAnyRole($roles){
        return $this->hasAnyAuthority($roles);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::isAnonymous()
     */
    function isAnonymous(){
        return (
            $this->getAuthentication()->getAuthenticationMode() == Authentication::ANONYMOUS ||
            !$this->isAuthenticated()
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::isAuthenticated()
     */
    function isAuthenticated(){
        return ($this->isRememberMe() || $this->isFullyAuthenticated());
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::isRememberMe()
     */
    function isRememberMe(){
        return $this->getAuthentication()->getAuthenticationMode() == Authentication::REMEMBER_ME;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::isFullyAuthenticated()
     */
    function isFullyAuthenticated(){
        return $this->getAuthentication()->getAuthenticationMode() == Authentication::FULLY_AUTHENTICATED;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\SecurityExpressionOperations::hasPermission()
     */
    function hasPermission($target, Permission $permission){
        $this->permissionEvaluator->hasPermission($this->authentication, $target, $permission);
    }
    
    /**
     * @return array<string> the list of authorities detained by the authenticated user
     */
    protected function getAuthoritySet() {
        $authorities =  $this->getAuthentication()->getAuthorities();
        
        if($authorities == null) {
            $authorities = array();
        }
        
        return $authorities;
    }
}
