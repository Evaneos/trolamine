<?php
namespace Trolamine\Layer;

use Pyrite\Layer\AbstractLayer;
use Pyrite\Response\ResponseBag;
use Trolamine\Core\Authentication\Authentication;
use Symfony\Component\HttpFoundation\Request;
use Pyrite\Container\Container;
use Trolamine\Core\Authentication\AnonymousAuthenticationToken;

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
    private static $sessionVarName = 'authentication';
    
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
            self::$sessionVarName= $sessionVarName;
        }
    }
    
    /**
     *
     * @return string
     */
    public static function getSessionVarName() {
        return self::$sessionVarName;
    }
    
    /**
     * 
     * @param string $sessionVarName
     */
    public static function setSessionVarName($sessionVarName) {
        self::$sessionVarName= $sessionVarName;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Pyrite\Layer\AbstractLayer::before()
     */
    protected function before(ResponseBag $responseBag) {
        /* @var $request Request */
        $request = $this->request;
        $authentication = $request->getSession()->get(self::$sessionVarName, null);
        if ($authentication == null) {
            $authentication = new AnonymousAuthenticationToken();
        }
        $responseBag->set(self::VAR_NAME, $authentication);
        $this->container->get('SecurityContext')->setAuthentication($authentication);
        $sc = $this->container->get('SecurityContext');
//         $sc->setAuthentication($authentication);
        var_dump($sc);
        $sc2 = $this->container->get('SecurityContext');
        var_dump($sc2);
        $sc3 = $this->container->get('SecurityContext');
        var_dump($sc3);
        die();        
        var_dump($authentication);
        var_dump($this->container->get('SecurityContext'));
        exit;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Pyrite\Layer\AbstractLayer::after()
     */
    protected function after(ResponseBag $responseBag) {
        /* @var $authentication Authentication */
        $authentication = $responseBag->get(self::VAR_NAME, null);
        
        /* @var $request Request */
        $request = $this->request;
        $request->getSession()->set(self::$sessionVarName, $authentication);
    }
    
}