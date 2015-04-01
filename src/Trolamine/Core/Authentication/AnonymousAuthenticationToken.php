<?php
namespace Trolamine\Core\Authentication;

class AnonymousAuthenticationToken extends AbstractAuthentication
{
        
    /**
     * This constructor can be safely used by any code that wishes to create a
     * <code>AnonymousAuthenticationToken</code>, as the {@link
     * #getAuthenticationMode()} will return <code>ANONYMOUS</code>.
     */
    public function __construct() {
        parent::__construct(null, Authentication::ANONYMOUS);
        $this->principal = null;
        $this->credentials = null;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\AbstractAuthentication::getCredentials()
     */
    public function getCredentials() {
        return null;
    }
    
    /**
     * Returns a default authorities array
     */
    function buildAuthorities() {
        $this->authorities = array();
    }
}
