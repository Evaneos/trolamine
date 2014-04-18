<?php
namespace Trolamine\Core\Operation;

interface MethodSecurityExpressionOperations extends SecurityExpressionOperations
{
    
    function setFilterObject($filterObject);

    function getFilterObject();

    function setReturnObject($returnObject);

    function getReturnObject();

    function getThis();
}
