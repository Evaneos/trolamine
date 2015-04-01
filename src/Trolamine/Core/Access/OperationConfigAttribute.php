<?php
namespace Trolamine\Core\Access;

use Trolamine\Core\Operation\Operation;

class OperationConfigAttribute
{
    
    /**
     * 
     * @var Operation
     */
    public $root;
    
    /**
     * 
     * @var string
     */
    public $method;
    
    /**
     * 
     * @var array
     */
    public $args;
    
    /**
     * 
     * @param Operation $root
     * @param string $method
     * @param array $args
     */
    function __construct(Operation $root, $method, array  $args = array()) {
        $this->root = $root;
        $this->method = $method;
        $this->args = $args;
    }    
}
