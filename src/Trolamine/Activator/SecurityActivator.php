<?php
namespace Trolamine\Activator;

use DICIT\Activators\AbstractActivatorDecorator;
use DICIT\Container;
use Trolamine\Factory\SecuredClassFactory;
use Trolamine\Core\Access\OperationConfigAttribute;
use DICIT\Util\ParamsResolver;
use Trolamine\Core\Operation\MethodSecurityExpressionRoot;

class SecurityActivator extends AbstractActivatorDecorator {

    /**
     * 
     * @var SecuredClassFactory
     */
    private $securedClassFactory;
    
    /**
     * 
     * @param SecuredClassFactory $securedClassFractory
     */
    public function __construct(SecuredClassFactory $securedClassFactory) {
        $this->securedClassFactory =$securedClassFactory;
    }
    
    /**
     * (non-PHPdoc)
     * @see \DICIT\Activators\AbstractActivatorDecorator::before()
     */
    protected function before(Container $container, &$serviceName, array &$serviceConfig) {
        
    }
    
    /**
     * (non-PHPdoc)
     * @see \DICIT\Activators\AbstractActivatorDecorator::after()
     */
    protected function after(Container $container, &$serviceName, array &$serviceConfig, &$returnObject) {
        $securityConfig = $this->getSecurityConfig($container, $serviceConfig);
        return $this->securedClassFactory->build($returnObject, $securityConfig);
    }
    
    /**
     * Returns the security config
     * 
     * @param  Container $container
     * @param  array     $serviceConfig
     * 
     * @return array
     */
    protected function getSecurityConfig(Container $container, array &$serviceConfig) {
        $securityConfig = $serviceConfig['security'];
        $realSecurityConfig = array();
        
        foreach ($securityConfig as $method=>$triggers) {
            $newTriggers = array();
            foreach ($triggers as $triggerName=>$functions) {
                $operations = array();
                foreach ($functions as $alias=>$params) {
                    if (array_key_exists('method', $params)) {
                        $realParams = ParamsResolver::resolveParams($container, $params);
                        
                        $root = null;
                        if (array_key_exists('operation', $realParams)) {
                            $root = $realParams['operation'];
                        } else {
                            $root = new MethodSecurityExpressionRoot();
                        }
                        
                        $args  = array();
                        if (array_key_exists('args', $realParams)) {
                            $args = $realParams['args'];
                        }
                        $operations[] = new OperationConfigAttribute($root, $realParams['method'], $args);
                    }
                }
                $newTriggers[$triggerName] = $operations;
            }
            $realSecurityConfig[$method] = $newTriggers;
        }
        
        return $realSecurityConfig;
    }
    
}