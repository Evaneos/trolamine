<?php
namespace Trolamine\Factory;

use Pyrite\Container\Container;

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
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Factory\SecuredClassFactory::build()
     */
    function build($className, array $constructorArgs = array(), array $securedParameters=array()) {
        
        $instance = $this->getSecuredInstance($className, $constructorArgs);
        $secured = new Secured($this->container->get('SecurityContext'), $securedParameters);
        $instance->setSecured($secured);
        
    }
    
    /**
     * Gets the secured instance for the class
     * 
     * @param string $className
     * @param array  $constructorArgs   The constructor args
     * 
     * @return the secured instance of the class
     */
    function getSecuredInstance($className, array $constructorArgs) {
        //TODO get the securedClassName
        $securedClassName = $className;
        
        $class = new \ReflectionClass($securedClassName);
        $instance = $class->newInstanceArgs($constructorArgs);
        
        return $instance;
    }
    
}