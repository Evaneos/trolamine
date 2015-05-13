<?php
namespace Trolamine\Core\Authentication;

class UsernamePasswordAuthenticationToken extends AbstractAuthentication
{
        
    /**
     * This constructor can be safely used by any code that wishes to create a
     * <code>UsernamePasswordAuthenticationToken</code>, as the {@link
     * #getAuthenticationMode()} will return <code>ANONYMOUS</code>.
     *
     * @param string $principal
     * @param string $credentials
     */
    public function __construct($principal, $credentials)
    {
        parent::__construct(null, Authentication::ANONYMOUS);
        $this->principal = $principal;
        $this->credentials = $credentials;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\AbstractAuthentication::getCredentials()
     */
    public function getCredentials()
    {
        return $this->credentials;
    }
    
    /**
     * Returns a default authorities array
     */
    public function buildAuthorities()
    {
        $this->authorities = array();
    }
}
