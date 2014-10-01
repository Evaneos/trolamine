<?php
namespace Trolamine\Factory;

use Trolamine\Core\SecurityContext;
use Trolamine\Core\Access\OperationConfigAttribute;
use Trolamine\Core\Access\OperationsUtil;

class Secured
{
    
    const PRE_AUTHORIZE = 'preAuthorize';
    const POST_AUTHORIZE = 'postAuthorize';
    const PRE_FILTER = 'preFilter';
    const POST_FILTER = 'postFilter';
    
    const ALL = '*';
    
    const PREFIX = '&';
    const RETURN_OBJECT_ALIAS = '&returnObject';
    
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
            foreach ($args as $key=>$arg) {
                $newArg = $arg;
                if ($arg == self::RETURN_OBJECT_ALIAS) {
                    $newArg = &$object;
                } else if (is_string($arg) && strpos($arg, self::PREFIX) === 0) {
                    $argName = substr($arg, 1);
                    if (array_key_exists($argName, $parameters)) {
                        $newArg = &$parameters[$argName];
                    }
                }
                $newArgs[$key] = $newArg;
            }
        }
        return $newArgs;
    }
    
    private function addConfigAttributes($configAttributes, $key, $actionName) {
        if (array_key_exists($key, $this->config)) {
            $actions = $this->config[$key];
            if (is_array($actions) && count($actions)>0 && array_key_exists($actionName,  $actions)) {
                $localConfigAttributes = $actions[$actionName];
                
                if (is_array($localConfigAttributes) && count($localConfigAttributes)>0) {
                    $configAttributes = array_merge($configAttributes, $localConfigAttributes);
                }
            }
        }
        return $configAttributes;
    }
    
    /**
     * Retrieves the conditions to check and checks them
     * 
     * @param string $method     the method name to check
     * @param array  $parameters the method parameters
     * @param string $actionName the security action (PRE/POST-AUTH/FILT)
     * @param mixed  $object     the reference object
     */
    protected function process($method, array $parameters, $actionName, $object=null) {
        $methodName = $method;
        
        if (is_array($this->config) && count($this->config)>0 && (array_key_exists(self::ALL, $this->config) || array_key_exists($methodName, $this->config))) {
            
            $configAttributes = array();
            $configAttributes = $this->addConfigAttributes($configAttributes, self::ALL, $actionName);
            $configAttributes = $this->addConfigAttributes($configAttributes, $methodName, $actionName);
            
            if (is_array($configAttributes) && count($configAttributes)>0) {
                
                //replace the ref args by the real value
                $newConfigAttributes= array();
                foreach ($configAttributes as $configAttribute) {
                    /* @var $check OperationConfigAttribute */
                    $args = $this->getParametersByRealValues($configAttribute->args, $parameters, $object);
                    
                    $newConfigAttribute = clone $configAttribute;
                    $newConfigAttribute->args = $args;
                    $newConfigAttributes[] = $newConfigAttribute;
                }
                
                if ($actionName == self::PRE_AUTHORIZE || $actionName == self::POST_AUTHORIZE) {    
                    $this->securityContext->getAccessDecisionManager()->decide(
                        $this->securityContext->getAuthentication(),
                        $object,
                        $newConfigAttributes
                    );
                } else if ($actionName == self::PRE_FILTER || $actionName == self::POST_FILTER) {
                    foreach ($newConfigAttributes as $attribute) {
                        // Update the parameters (only objects)
                        OperationsUtil::evaluate($this->securityContext->getAuthentication(), $attribute);
                    }
                }
            }
        }
    }
    
    /**
     * The PreAuthorize method to be called before the real method call
     */
    public function preAuthorize($method, array $parameters=array()) {
        $this->process($method, $parameters, self::PRE_AUTHORIZE);
    }
    
    /**
     * The PostAuthorize method to be called after the real method has returned a value
     * 
     * @param mixed $response the response of the method to secure
     */
    public function postAuthorize($method, array $parameters=array(), $response) {
        $this->process($method, $parameters, self::POST_AUTHORIZE, $response);
    }
    
    /**
     * The PreFilter method to be called before the real method call
     */
    public function preFilter($method, array $parameters=array()) {
        $this->process($method, $parameters, self::PRE_FILTER);
        return $parameters;
    }
    
    /**
     * The PostFilter method to be called after the real method has returned a value
     *
     * @param mixed $response the response of the method to secure
     */
    public function postFilter($method, array $parameters=array(), $response) {
        //TODO modify the response
        return $response;
    }
}
