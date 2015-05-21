<?php
namespace Trolamine\Core\Access;

use Trolamine\Core\Authentication\Authentication;

class OperationsUtil
{

    /**
     * Evaluates the operation
     *
     * @param  Authentication           $authentication
     * @param  OperationConfigAttribute $attribute
     * @return boolean
     */
    public static function evaluate(Authentication $authentication, OperationConfigAttribute $attribute)
    {
        $root = $attribute->root;
        $root->setAuthentication($authentication);

        $newArgs = array();
        foreach ($attribute->args as $key => $arg) {
            if (is_object($arg)) {
                $newArgs[$key] = $attribute->args[$key];
            } else {
                $newArgs[$key] = &$attribute->args[$key];
            }
        }
        return call_user_func_array(array($root, $attribute->method), $newArgs);
    }
}
