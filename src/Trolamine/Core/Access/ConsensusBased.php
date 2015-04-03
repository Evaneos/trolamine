<?php
namespace Trolamine\Core\Access;

use Trolamine\Core\Authentication\Authentication;
use Trolamine\Core\Exception\AccessDeniedException;

/**
 * Simple concrete implementation of  {@link org.springframework.security.access.AccessDecisionManager} that uses a
 * consensus-based approach.
 * <p>
 * "Consensus" here means majority-rule (ignoring abstains) rather than unanimous agreement (ignoring abstains).
 * If you require unanimity, please see {@link UnanimousBased}.
 */
class ConsensusBased extends AbstractAccessDecisionManager
{

    /**
     * 
     * @var string
     */
    private $allowIfEqualGrantedDeniedDecisions = true;

    /**
     * This concrete implementation simply polls all configured  {@link AccessDecisionVoter}s and upon
     * completion determines the consensus of granted against denied responses.
     * <p>
     * If there were an equal number of grant and deny votes, the decision will be based on the
     * {@link #isAllowIfEqualGrantedDeniedDecisions()} property (defaults to true).
     * <p>
     * If every <code>AccessDecisionVoter</code> abstained from voting, the decision will be based on the
     * {@link #isAllowIfAllAbstainDecisions()} property (defaults to false).
     *
     * @param  Authentication         $authentication   the caller invoking the method (not null)
     * @param  object                 $object           the secured object being called
     * @param  array<ConfigAttribute> $configAttributes the configuration attributes associated with the secured object being invoked
     *
     * @throws AccessDeniedException  if access is denied as the authentication does not hold a required authority or
     *                                ACL privilege
     */
    public function decide(Authentication $authentication, $object, array $configAttributes) {
        
        $grant = 0;
        $deny = 0;

        $voters = $this->getDecisionVoters();
        foreach($voters as $voter) {
            /* @var $voter AccessDecisionVoter */
            $result = $voter->vote($authentication, $object, $configAttributes);

            switch ($result) {
                case AccessDecisionVoter::ACCESS_GRANTED:
                    $grant++;
                    break;
    
                case AccessDecisionVoter::ACCESS_DENIED:
                    $deny++;
                    break;
    
                default:
                    break;
            }
        }

        if ($grant > $deny) {
            return;
        }

        if ($deny > $grant) {
            throw new AccessDeniedException(AbstractAccessDecisionManager::ACCESSDENIED);
        }

        if (($grant == $deny) && ($grant != 0)) {
            if ($this->allowIfEqualGrantedDeniedDecisions) {
                return;
            } else {
                throw new AccessDeniedException(AbstractAccessDecisionManager::ACCESSDENIED);
            }
        }

        // To get this far, every AccessDecisionVoter abstained
        $this->checkAllowIfAllAbstainDecisions();
    }

    /**
     * 
     * @return boolean
     */
    public function isAllowIfEqualGrantedDeniedDecisions() {
        return $this->allowIfEqualGrantedDeniedDecisions;
    }

    /**
     * Sets the strategy in case of equality
     * 
     * @param boolean $allowIfEqualGrantedDeniedDecisions
     */
    public function setAllowIfEqualGrantedDeniedDecisions($allowIfEqualGrantedDeniedDecisions) {
        $this->allowIfEqualGrantedDeniedDecisions = $allowIfEqualGrantedDeniedDecisions;
    }
}