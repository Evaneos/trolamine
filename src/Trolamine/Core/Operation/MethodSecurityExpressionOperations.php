<?php
namespace Trolamine\Core\Operation;

interface MethodSecurityExpressionOperations extends SecurityExpressionOperations
{
    
    public function setFilterObject($filterObject);

    public function getFilterObject();

    public function setReturnObject($returnObject);

    public function getReturnObject();

    public function getThis();
}
