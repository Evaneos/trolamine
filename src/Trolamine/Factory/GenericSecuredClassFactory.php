<?php
namespace Trolamine\Factory;

class GenericSecuredClassFactory implements SecuredClassFactory {
    
    /**
     * The container
     *
     * @var Container
     */
    private $container;
    
    /**
     * Constructor
     *
     * @param Container $container
     */
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    function build($className, array $securedParameters=array()) {
        
        $ref = new \ReflectionClass($className);
        
        foreach ($securedParameters as $methodName=>$secureActions) {
            if (!$ref->hasMethod($name)) {
                throw new \BadMethodCallException('Method "'.$methodName.'" doesn\'t exist or is not public for class "'.$className.'"');
            }
            
            //Ignores if no parameters are passed
            if (is_array($secureActions) && count($secureActions)>0) {
                if (array_key_exists(Secured::PRE_AUTHORIZE, $secureActions)) {
                    $preAuthorizeConfigAttributes = $secureActions[Secured::PRE_AUTHORIZE];
                    
                    if (is_array($preAuthorizeConfigAttributes) && count($preAuthorizeConfigAttributes)>0) {
                        
                    }
                }
                
                if (array_key_exists(Secured::POST_AUTHORIZE, $secureActions)) {
                    $postAuthorizeConfigAttributes = $secureActions[Secured::POST_AUTHORIZE];
                }
                
                //ignores other instruction keys
            }
        }
        
    }
    
}