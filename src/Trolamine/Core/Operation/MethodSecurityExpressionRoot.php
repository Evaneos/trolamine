<?php
namespace Trolamine\Core\Operation;

use Trolamine\Core\Authentication\Authentication;

class MethodSecurityExpressionRoot extends SecurityExpressionRoot implements MethodSecurityExpressionOperations {
    
    private $filterObject;
    private $returnObject;
    private $target;

    /**
     * 
     * @param Authentication $a
     */
    public function __construct(Authentication $a=null) {
        parent::__construct($a);
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\MethodSecurityExpressionOperations::setFilterObject()
     */
    public function setFilterObject($filterObject) {
        $this->filterObject = $filterObject;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\MethodSecurityExpressionOperations::getFilterObject()
     */
    public function getFilterObject() {
        return $this->filterObject;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\MethodSecurityExpressionOperations::setReturnObject()
     */
    public function setReturnObject($returnObject) {
        $this->returnObject = $returnObject;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\MethodSecurityExpressionOperations::getReturnObject()
     */
    public function getReturnObject() {
        return $this->returnObject;
    }

    /**
     * Sets the "this" property for use in expressions. Typically this will be the "this" property of
     * the {@code JoinPoint} representing the method invocation which is being protected.
     *
     * @param object target the target object on which the method in is being invoked.
     */
    function setThis($target) {
        $this->target = $target;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\MethodSecurityExpressionOperations::getThis()
     */
    public function getThis() {
        return $this->target;
    }
}

