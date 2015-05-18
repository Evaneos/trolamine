<?php
namespace Trolamine\Core\Access;

use Trolamine\Core\Exception\AccessDeniedException;

/**
 * Abstract implementation of {@link AccessDecisionManager}.
 * <p/>
 * Handles configuration of a bean context defined list of
 * {@link AccessDecisionVoter}s and the access control behaviour if all voters
 * abstain from voting (defaults to deny access).
 * </p>
 */
abstract class AbstractAccessDecisionManager implements AccessDecisionManager
{

    const ACCESSDENIED = 'Access Denied!';
    
    /**
     *
     * @var array<AccessDecisionVoter>
     */
    private $decisionVoters;

    /**
     *
     * @var boolean
     */
    private $allowIfAllAbstainDecisions = false;

    public function __construct(array $decisionVoters=array())
    {
        $this->decisionVoters = $decisionVoters;
    }

    protected function checkAllowIfAllAbstainDecisions()
    {
        if (!$this->isAllowIfAllAbstainDecisions()) {
            throw new AccessDeniedException(self::ACCESSDENIED);
        }
    }

    public function getDecisionVoters()
    {
        return $this->decisionVoters;
    }

    public function isAllowIfAllAbstainDecisions()
    {
        return $this->allowIfAllAbstainDecisions;
    }

    public function setAllowIfAllAbstainDecisions($allowIfAllAbstainDecisions)
    {
        $this->allowIfAllAbstainDecisions = $allowIfAllAbstainDecisions;
    }

    public function addDecisionVoter(AccessDecisionVoter $voter)
    {
        $this->decisionVoters[] = $voter;
    }
    
    public function setDecisionVoters(array $newList)
    {
        $this->decisionVoters = $newList;
    }

    public function supports($attribute)
    {
        $voters = $this->decisionVoters;
        foreach ($voters as $voter) {
            /* @var $voter AccessDecisionVoter */
            if ($voter->supports($attribute)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @todo: modifier le code pour correspondre à la docs. (à revoir)
     * Iterates through all <code>AccessDecisionVoter</code>s and ensures each can support the presented class.
     * <p/>
     * If one or more voters cannot support the presented class, <code>false</code> is returned.
     * </p>
     *
     * @param  string  $class the type of secured object being presented
     *
     * @return boolean true if this type is supported
     */
    public function supportsClass($class)
    {
        $voters = $this->decisionVoters;
        foreach ($voters as $voter) {
            /* @var $voter AccessDecisionVoter */
            if ($voter->supportsClass($class)) {
                return true;
            }
        }

        return true;
    }
}
