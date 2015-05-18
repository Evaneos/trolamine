<?php
namespace Trolamine\Core\Authentication;

/**
 * Provides core user information.
 *
 * <p>
 * Implementations are not used directly by Spring Security for security
 * purposes. They simply store user information which is later encapsulated
 * into {@link Authentication} objects. This allows non-security related user
 * information (such as email addresses, telephone numbers etc) to be stored
 * in a convenient location.
 * <p>
 * Concrete implementations must take particular care to ensure the non-null
 * contract detailed for each method is enforced.
 *
 * @see \Trolamine\Core\Authentication\UserDetailsService
 */
interface UserDetails
{

    /**
     * Returns the encapsulated user object
     *
     * @return object the user object encapsulated
     */
    public function getUser();

    /**
     * Returns the password used to authenticate the user.
     *
     * @return string the password
     */
    public function getPassword();


    /**
     * Returns the password salt used to authenticate the user.
     *
     * @return string the password salt
     */
    public function getSalt();

    /**
     * Returns the username used to authenticate the user. Cannot return <code>null</code>.
     *
     * @return string the username (never <code>null</code>)
     */
    public function getUsername();

    /**
     * Indicates whether the user's account has expired. An expired account cannot be authenticated.
     *
     * @return string <code>true</code> if the user's account is valid (ie non-expired), <code>false</code> if no longer valid (ie expired)
     */
    public function isAccountNonExpired();

    /**
     * Indicates whether the user is locked or unlocked. A locked user cannot be authenticated.
     *
     * @return string <code>true</code> if the user is not locked, <code>false</code> otherwise
     */
    public function isAccountNonLocked();

    /**
     * Indicates whether the user's credentials (password) has expired. Expired credentials prevent
     * authentication.
     *
     * @return string <code>true</code> if the user's credentials are valid (ie non-expired), <code>false</code> if no longer valid (ie expired)
    */
    public function isCredentialsNonExpired();

    /**
     * Indicates whether the user is enabled or disabled. A disabled user cannot be authenticated.
     *
     * @return string <code>true</code> if the user is enabled, <code>false</code> otherwise
    */
    public function isEnabled();
}
