<?php
namespace Trolamine\Factory;

use Trolamine\Core\SecurityContext;
use Trolamine\Core\Access\OperationConfigAttribute;
class Secured {
    
    const PRE_AUTHORIZE = 'preAuthorize';
    const POST_AUTHORIZE = 'postAuthorize';
    const PRE_FILTER = 'preFilter';
    const POST_FILTER = 'postFilter';
    
    const PREFIX = '#';
    const RETURN_OBJECT_ALIAS = '#returnObject';
    
    /**
     * The associative array of parameters :
     * array (
     *     ['methodNameToSecure'] => array (
     *         ['preAuthorize'] => array<ConfigAttribute>(...),
     *         ['postAuthorize'] => array<ConfigAttribute>(...)
     *     ),
     *     ...
     * )
     * 
     * @var array
     */
    protected $config;
    
    /**
     * The securityContext
     * 
     * @var SecurityContext
     */
    protected $securityContext;
    
    /**
     * Constructor
     * 
     * @param SecurityContext $securityContext
     * @param array $config
     */
    public function __construct(SecurityContext $securityContext, array $config = array()) {
        $this->securityContext = $securityContext;
        $this->config = $config;
    }
    
    protected function getParametersByRealValues($args, array $parameters, $object=null) {
        $newArgs = array();
        if ($args != null && is_array($args) && count($args)>0) {
            foreach ($args as $arg) {
                $newArg = $arg;
                if ($arg == self::RETURN_OBJECT_ALIAS) {
                    $newArg = $object;
                } else if (is_string($arg) && strpos($arg, self::PREFIX) === 0) {
                    $argName = substr($arg, 1);
                    if (array_key_exists($argName, $parameters)) {
                        $newArg = $parameters[$argName];
                    }
                }
                $newArgs[] = $newArg;
            }
        }
        return $newArgs;
    }
    
    /**
     * Retrieves the conditions to check and checks them
     * 
     * @param string $method     the method name to check
     * @param array  $parameters the method parameters
     * @param string $actionName the security action (PRE/POST-AUTH/FILT)
     * @param mixed  $object     the reference object
     */
    protected function check($method, array $parameters, $actionName, $object=null) {
        $methodName = $method;
        
        if (is_array($this->config) && count($this->config)>0 && array_key_exists($methodName, $this->config)) {
            $actions = $this->config[$methodName];
            if (is_array($actions) && count($actions)>0 && array_key_exists($actionName,  $actions)) {
                $checks = $actions[$actionName];
                if (is_array($checks) && count($checks)>0) {
                    
                    //replace the ref args by the real value
                    $newChecks= array();
                    foreach ($checks as $check) {
                        /* @var $check OperationConfigAttribute */
                        $args = $this->getParametersByRealValues($check->args, $parameters, $object);
                        
                        $newCheck = clone $check;
                        $newCheck->args = $args;
                        $newChecks[] = $newCheck;
                    }
                    
                    $this->securityContext->getAccessDecisionManager()->decide(
                        $this->securityContext->getAuthentication(),
                        $object,
                        $newChecks
                    );
                }
            }
        }
    }
    
    /**
     * The PreAuthorize method to be called before the real method call
     */
    public function preAuthorize($method, array $parameters=array()) {
        $this->check($method, $parameters, self::PRE_AUTHORIZE);
    }
    
    /**
     * The PostAuthorize method to be called after the real method has returned a value
     * 
     * @param mixed $response the response of the method to secure
     */
    public function postAuthorize($method, array $parameters=array(), $response) {
        $this->check($method, $parameters, self::POST_AUTHORIZE, $response);
    }
    
}