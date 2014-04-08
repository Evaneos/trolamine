<?php
namespace Trolamine\Layer;

use Pyrite\Layer\AbstractLayer;
use Pyrite\Response\ResponseBag;
use Trolamine\Core\Authentication\Authentication;
use Symfony\Component\HttpFoundation\Request;
use Pyrite\Container\Container;

class AuthenticationLayer extends AbstractLayer {
    
    /**
     * The authentication var name in the ResponseBag
     * 
     * @var string
     */
    const VAR_NAME = 'authentication';
    
    /**
     * The authentication var name in the session
     * 
     * @var string
     */
    private $sessionVarName = 'authentication';
    
    /**
     * The container
     * 
     * @var Container
     */
    private $container; 
    
    /**
     * Constructor
     * 
     * @param Container $container
     * @param string    $sessionVarName
     */
    public function __construct(Container $container, $sessionVarName = null) {
        $this->container = $container;
        if ($sessionVarName != null) {
            $this->sessionVarName= $sessionVarName;
        }
    }
    
    /**
     * 
     * @param string $sessionVarName
     */
    public function setSessionVarName($sessionVarName) {
        $this->sessionVarName= $sessionVarName;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Pyrite\Layer\AbstractLayer::before()
     */
    protected function before(ResponseBag $responseBag) {
        /* @var $request Request */
        $request = $this->request;
        $authentication = $request->getSession()->get($this->sessionVarName);
        $responseBag->set(VAR_NAME, $authentication);
        $this->container->get('SecurityContext')->setAuthentication($authentication);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Pyrite\Layer\AbstractLayer::after()
     */
    protected function after(ResponseBag $responseBag) {
        /* @var $authentication Authentication */
        $authentication = $responseBag->get(VAR_NAME, null);
        
        /* @var $request Request */
        $request = $this->request;
        $request->getSession()->set($this->sessionVarName, $authentication);
    }
    
}