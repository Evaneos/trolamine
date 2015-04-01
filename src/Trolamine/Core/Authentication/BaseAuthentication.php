<?php
namespace Trolamine\Core\Authentication;

/**
 * A concrete authentication object
 * 
 * @author Remi
 *
 */
class BaseAuthentication extends AbstractAuthentication
{

    public function __construct(UserDetails $user=null, $mode, array $authorities=array()) {
        parent::__construct($user, $mode, $authorities);
        if ($user instanceof UserDetails) {
            $this->principal = $user->getUsername();
        }
    }
    
    public function getCredentials() {
        //We never return the password in this context
        return null;
    }
    
    /**
     * 
     * @param array $authorities
     */
    function setAuthorities(array $authorities) {
        $this->authorities = $authorities;
    }
}
