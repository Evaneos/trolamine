<?php
namespace Trolamine\Core\Access;

use Trolamine\Core\Authentication\Authentication;
use Trolamine\Core\Exception\AccessDeniedException;
/**
 * Simple concrete implementation of  {@link org.springframework.security.access.AccessDecisionManager} that grants access if any
 * <code>AccessDecisionVoter</code> returns an affirmative response.
 */
class AffirmativeBased extends AbstractAccessDecisionManager {

    public function __construct(array $decisionVoters) {
        parent::__construct($decisionVoters);
    }

    /**
     * This concrete implementation simply polls all configured  {@link AccessDecisionVoter}s and grants access
     * if any <code>AccessDecisionVoter</code> voted affirmatively. Denies access only if there was a deny vote AND no
     * affirmative votes.<p>If every <code>AccessDecisionVoter</code> abstained from voting, the decision will
     * be based on the {@link #isAllowIfAllAbstainDecisions()} property (defaults to false).</p>
     *
     * @param  Authentication         $authentication   the caller invoking the method (not null)
     * @param  object                 $object           the secured object being called
     * @param  array<ConfigAttribute> $configAttributes the configuration attributes associated with the secured object being invoked
     *
     * @throws AccessDeniedException  if access is denied as the authentication does not hold a required authority or
     *         ACL privilege
     */
    public function decide(Authentication $authentication, $object, array $configAttributes) {
        $deny = 0;

        $voters = $this->getDecisionVoters();
        foreach($voters as $voter) {
            /* @var $voter AccessDecisionVoter */
            $result = $voter->vote($authentication, $object, $configAttributes);

            switch ($result) {
                case AccessDecisionVoter::ACCESS_GRANTED:
                    return;

                case AccessDecisionVoter::ACCESS_DENIED:
                    $deny++;
                    break;

                default:
                    break;
            }
        }

        if (deny > 0) {
            throw new AccessDeniedException(AbstractAccessDecisionManager::accessDenied);
        }

        // To get this far, every AccessDecisionVoter abstained
        $this->checkAllowIfAllAbstainDecisions();
    }
}