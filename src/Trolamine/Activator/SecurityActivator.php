<?php
namespace Trolamine\Activator;

use DICIT\Activators\AbstractActivatorDecorator;
use DICIT\Container;
use Trolamine\Factory\SecuredClassFactory;

class SecurityActivator extends AbstractActivatorDecorator {

    /**
     * 
     * @var SecuredClassFactory
     */
    private $securedClassFractory;
    
    /**
     * 
     * @param SecuredClassFactory $securedClassFractory
     */
    public function __construct(SecuredClassFactory $securedClassFractory) {
        $this->securedClassFractory =$securedClassFractory;
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
        $securityConfig = $this->getSecurityConfig($serviceConfig);
        return $this->securedClassFractory->build($returnObject, $securityConfig);
        
    }
    
    protected function getSecurityConfig(array &$serviceConfig) {
        $securityConfig = $serviceConfig['remote'];
        
        //TODO translate the parameters to create the OperationConfig objects
        
        return $securityConfig;
    }
    
}