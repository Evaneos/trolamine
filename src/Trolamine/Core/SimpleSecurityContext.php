<?php
namespace Trolamine\Core;

use Trolamine\Core\Authentication\Authentication;
use Trolamine\Core\Access\AccessDecisionManager;
use Trolamine\Core\Operation\MethodSecurityExpressionRoot;
use Trolamine\Core\Exception\InsufficientAuthenticationException;
use Trolamine\Core\Exception\AccessDeniedException;
use Trolamine\Core\Access\OperationConfigAttribute;

class SimpleSecurityContext implements SecurityContext
{
    
    /**
     *
     * @var Authentication
     */
    protected $authentication;
    
    /**
     *
     * @var AccessDecisionManager
     */
    protected $accessDecisionManager;
    
    /**
     * Constructor
     *
     * @param AccessDecisionManager $accessDecisionManager
     */
    public function __construct(AccessDecisionManager $accessDecisionManager)
    {
        $this->accessDecisionManager = $accessDecisionManager;
    }
    
    public function setAuthentication(Authentication $authentication)
    {
        $this->authentication = $authentication;
    }
    
    public function getAuthentication()
    {
        return $this->authentication;
    }
    
    
    public function getAccessDecisionManager()
    {
        return $this->accessDecisionManager;
    }
    
    public function hasRole($roleName)
    {
        $roleOperation = new MethodSecurityExpressionRoot();
        $roleConfigAttribute = new OperationConfigAttribute($roleOperation, 'hasRole', array($roleName));
        
        try {
            $this->accessDecisionManager->decide($this->authentication, null, array($roleConfigAttribute));
        } catch (AccessDeniedException $ade) {
            return false;
        } catch (InsufficientAuthenticationException $ade) {
            return false;
        } catch (\Exception $e) {
            throw $e;
        }
        
        return true;
    }

    public function hasAnyRole($roles)
    {
        $roleOperation = new MethodSecurityExpressionRoot();
        $roleConfigAttribute = new OperationConfigAttribute($roleOperation, 'hasAnyRole', array($roles));
        
        try {
            $this->accessDecisionManager->decide($this->authentication, null, array($roleConfigAttribute));
        } catch (AccessDeniedException $ade) {
            return false;
        } catch (InsufficientAuthenticationException $ade) {
            return false;
        } catch (\Exception $e) {
            throw $e;
        }
        
        return true;
    }
}
