<?php
namespace Trolamine\Core\Access;

use Trolamine\Core\Authentication\Authentication;

/**
 * Indicates a class is responsible for voting on authorization decisions.
 * <p>
 * The coordination of voting (ie polling {@code AccessDecisionVoter}s,
 * tallying their responses, and making the final authorization decision) is
 * performed by an {@link AccessDecisionManager}.
 */
interface AccessDecisionVoter {
    
    const ACCESS_GRANTED = 1;
    const ACCESS_ABSTAIN = 0;
    const ACCESS_DENIED = -1;

    /**
     * Indicates whether this {@code AccessDecisionVoter} is able to vote on the passed {@code ConfigAttribute}.
     * <p>
     * This allows the {@code AccessDecisionManager} caller  to check every configuration attribute can be consumed by
     * the configured {@code AccessDecisionManager} and/or {@code RunAsManager} and/or {@code AfterInvocationManager}.
     *
     * @param  ConfigAttribute $attribute a configuration attribute that has been configured against the
     *                         {@code AccessDecisionManager} caller
     *
     * @return boolean         true if this {@code AccessDecisionVoter} can support the passed configuration attribute
     */
    function supports($attribute);

    /**
     * Indicates whether the {@code AccessDecisionVoter} implementation is able to provide access control
     * votes for the indicated secured object type.
     *
     * @param  string  $class the class that is being queried
     *
     * @return boolean true if the implementation can process the indicated class
    */
    function supportsClass($class);

    /**
     * Indicates whether or not access is granted.
     * <p>
     * The decision must be affirmative ({@code ACCESS_GRANTED}), negative ({@code ACCESS_DENIED})
     * or the {@code AccessDecisionVoter} can abstain ({@code ACCESS_ABSTAIN}) from voting.
     * Under no circumstances should implementing classes return any other value. If a weighting of results is desired,
     * this should be handled in a custom {@link org.springframework.security.access.AccessDecisionManager} instead.
     * <p>
     * Unless an {@code AccessDecisionVoter} is specifically intended to vote on an access control
     * decision due to a passed method invocation or configuration attribute parameter, it must return
     * {@code ACCESS_ABSTAIN}. This prevents the coordinating {@code AccessDecisionManager} from counting
     * votes from those {@code AccessDecisionVoter}s without a legitimate interest in the access control
     * decision.
     * <p>
     * Whilst the secured object (such as a {@code MethodInvocation}) is passed as a parameter to maximise flexibility
     * in making access control decisions, implementing classes should not modify it or cause the represented invocation
     * to take place (for example, by calling {@code MethodInvocation.proceed()}).
     *
     * @param  Authentication         $authentication the caller making the invocation
     * @param  object                 $object         the secured object being invoked
     * @param  array<ConfigAttribute> $attributes     the configuration attributes associated with the secured object
     *
     * @return int                    either {@link #ACCESS_GRANTED}, {@link #ACCESS_ABSTAIN} or {@link #ACCESS_DENIED}
     */
    function vote(Authentication $authentication, $object, array $attributes);
}
