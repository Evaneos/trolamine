<?php
namespace Trolamine\Core\Access;

use Trolamine\Core\Authentication\Authentication;

class OperationDecisionVoter implements AccessDecisionVoter{
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Access\AccessDecisionVoter::supports()
     */
    function supports($attribute) {
        return ($attribute instanceof OperationConfigAttribute);
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Access\AccessDecisionVoter::supportsClass()
     */
    function supportsClass($class) {
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Access\AccessDecisionVoter::vote()
     */
    function vote(Authentication $authentication, $object, array $attributes) {
        
        $denied = false;
        
        foreach ($attributes as $attribute) {
            /* @var $attribute OperationConfigAttribute */
            
            if ($this->supports($attribute)) {
                if (OperationsUtil::evaluate($authentication, $attribute)) {
                    return AccessDecisionVoter::ACCESS_GRANTED;
                } else {
                    $denied = true;
                }
            }
        }
        
        //if $denied is false, no attribute was supported, else it returns ACCESS_DENIED
        return ($denied) ? AccessDecisionVoter::ACCESS_DENIED : AccessDecisionVoter::ACCESS_ABSTAIN ;
    }
}
