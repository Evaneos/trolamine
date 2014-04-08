<?php
namespace Trolamine\Core\Access;

use Trolamine\Core\Authentication\Authentication;
use Trolamine\Core\Exception\AccessDeniedException;

/**
 * Simple concrete implementation of  {@link org.springframework.security.access.AccessDecisionManager} that requires all
 * voters to abstain or grant access.
 */
class UnanimousBased extends AbstractAccessDecisionManager {
    
    public function __construct(array $decisionVoters) {
        parent::__construct($decisionVoters);
    }

    /**
     * This concrete implementation polls all configured  {@link AccessDecisionVoter}s for each {@link
     * ConfigAttribute} and grants access if <b>only</b> grant (or abstain) votes were received.
     * <p>
     * Other voting implementations usually pass the entire list of <tt>ConfigAttribute</tt>s to the
     * <code>AccessDecisionVoter</code>. This implementation differs in that each <code>AccessDecisionVoter</code>
     * knows only about a single <code>ConfigAttribute</code> at a time.
     * <p>
     * If every <code>AccessDecisionVoter</code> abstained from voting, the decision will be based on the
     * {@link #isAllowIfAllAbstainDecisions()} property (defaults to false).
     *
     * @param  Authentication         $authentication   the caller invoking the method (not null)
     * @param  object                 $object           the secured object being called
     * @param  array<ConfigAttribute> $configAttributes the configuration attributes associated with the secured object being invoked
     *
     * @throws AccessDeniedException  if access is denied as the authentication does not hold a required authority or ACL privilege
     */
    public function decide(Authentication $authentication, $object, array $configAttributes) {
        
        $grant = 0;
    
        foreach ($configAttributes as $attribute) {
            /* @var $attribute ConfigAttribute */
            $singleAttributeList = array($attribute);
    
            $voters = $this->getDecisionVoters();
            foreach($voters as $voter) {
                /* @var $voter AccessDecisionVoter */
                $result = $voter->vote($authentication, $object, $singleAttributeList);
    
                switch ($result) {
                    case AccessDecisionVoter::ACCESS_GRANTED:
                        $grant++;
                        break;
    
                    case AccessDecisionVoter::ACCESS_DENIED:
                        throw new AccessDeniedException(AbstractAccessDecisionManager::accessDenied);
    
                    default:
                        break;
                }
            }
        }
    
        // To get this far, there were no deny votes
        if ($grant > 0) {
            return;
        }
    
        // To get this far, every AccessDecisionVoter abstained
        $this->checkAllowIfAllAbstainDecisions();
    }
}