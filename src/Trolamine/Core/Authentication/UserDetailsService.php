<?php
namespace Trolamine\Core\Authentication;

interface UserDetailsService
{
    
    /**
     * Locates the user based on the username. In the actual implementation, the search may possibly be case
     * insensitive, or case insensitive depending on how the implementation instance is configured. In this case, the
     * <code>UserDetails</code> object that comes back may have a username that is of a different case than what was
     * actually requested..
     *
     * @param string username the username identifying the user whose data is required.
     *
     * @return UserDetails a fully populated user record (never <code>null</code>)
     *
     * @throws InsufficientAuthenticationException if the user could not be found or the user has no GrantedAuthority
     */
    public function loadUserByUsername($username);
}
