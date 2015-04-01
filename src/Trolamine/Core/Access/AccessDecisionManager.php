<?php
namespace Trolamine\Core\Access;

use Trolamine\Core\Authentication\Authentication;
use Trolamine\Core\Exception\AccessDeniedException;
use Trolamine\Core\Exception\InsufficientAuthenticationException;

/**
 * Makes a final access control (authorization) decision.
 */
interface AccessDecisionManager
{
    
    /**
     * Resolves an access control decision for the passed parameters.
     *
     * @param  Authentication         $authentication   the caller invoking the method (not null)
     * @param  object                 $object           the secured object being called
     * @param  array<ConfigAttribute> $configAttributes the configuration attributes associated with the secured object being invoked
     *
     * @throws AccessDeniedException  if access is denied as the authentication does not hold a required authority or
     *         ACL privilege
     * @throws InsufficientAuthenticationException if access is denied as the authentication does not provide a
     *         sufficient level of trust
     */
    function decide(Authentication $authentication, $object, array $configAttributes);

    /**
     * Indicates whether this <code>AccessDecisionManager</code> is able to process authorization requests
     * presented with the passed <code>ConfigAttribute</code>.<p>This allows the
     * <code>AccessDecisionManager</code> caller to check every configuration attribute can be consumed by the
     * configured <code>AccessDecisionManager</code> and/or <code>RunAsManager</code> and/or
     * <code>AfterInvocationManager</code>.</p>
     *
     * @param ConfigAttribute   $attribute a configuration attribute that has been configured against the
     *                                     <code>AccessDecisionManager</code> caller 
     *
     * @return boolean          true if this <code>AccessDecisionManager</code> can support the passed configuration attribute
     */
    function supports($attribute);

    /**
     * Indicates whether the <code>AccessDecisionManager</code> implementation is able to provide access
     * control decisions for the indicated secured object type.
     *
     * @param string   $class the class that is being queried
     *
     * @return boolean <code>true</code> if the implementation can process the indicated class
    */
    function supportsClass($class);
}
