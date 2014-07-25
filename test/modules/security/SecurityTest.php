<?php
use Trolamine\Core\Operation\MethodSecurityExpressionRoot;
use Trolamine\Core\Authentication\BaseAuthentication;
use Trolamine\Core\Authentication\Authentication;
use Trolamine\Core\Access\OperationDecisionVoter;
use Trolamine\Core\Access\OperationConfigAttribute;
use Trolamine\Core\Access\AccessDecisionVoter;
use Trolamine\Core\Access\AccessDecisionManager;
use Trolamine\Core\Access\UnanimousBased;
use Trolamine\Core\Access\ConsensusBased;
use Trolamine\Core\Access\AffirmativeBased;
use Trolamine\Factory\Secured;
use Trolamine\Core\SimpleSecurityContext;

class SecurityTest extends PHPUnit_Framework_TestCase
{
    
    private $authentication = null;
    private $root = null;
    
    private $voter = null;
    
    private $adminConfigAttribute = null;
    private $userConfigAttribute = null;
    private $testConfigAttribute = null;
    private $adminUserConfigAttribute = null;
    
    public function setUp() {
        $this->authentication = new BaseAuthentication(null, Authentication::FULLY_AUTHENTICATED);
        $this->authentication->setAuthorities(array('ROLE_ADMIN', 'ROLE_USER'));
        $this->root = new MethodSecurityExpressionRoot() ;
        
        $this->voter = new OperationDecisionVoter();
        
        $this->adminConfigAttribute = new OperationConfigAttribute($this->root, 'hasRole', array('ROLE_ADMIN'));
        $this->userConfigAttribute = new OperationConfigAttribute($this->root, 'hasRole', array('ROLE_USER'));
        $this->testConfigAttribute = new OperationConfigAttribute($this->root, 'hasRole', array('ROLE_TEST'));
        $this->adminUserConfigAttribute = new OperationConfigAttribute($this->root, 'hasAnyRole', array(array('ROLE_ADMIN', 'ROLE_USER')));
        
    }
    
    public function testAuthenticationMode() {
        $root = $this->root;
        $root->setAuthentication($this->authentication);
        
        $this->assertTrue($root->isAuthenticated());
        $this->assertTrue($root->isFullyAuthenticated());
        $this->assertFalse($root->isRememberMe());
        $this->assertFalse($root->isAnonymous());
        
        $this->assertTrue($root->hasRole('ROLE_ADMIN'));
        $this->assertTrue($root->hasRole('ROLE_USER'));
        $this->assertFalse($root->hasRole('ROLE_TEST'));
        
        $this->assertTrue($root->hasAnyRole(array('ROLE_ADMIN', 'ROLE_TEST')));
        $this->assertTrue($root->hasAnyRole(array('ROLE_USER', 'ROLE_TEST')));
        $this->assertTrue($root->hasAnyRole(array('ROLE_USER', 'ROLE_ADMIN')));
        $this->assertFalse($root->hasAnyRole(array('ROLE_TEST', 'ROLE_TEST2')));
    }
    
    public function testVoter() {
        $voter = $this->voter;
        
        $adminConfigAttribute = $this->adminConfigAttribute;
        $userConfigAttribute = $this->userConfigAttribute ;
        $testConfigAttribute = $this->testConfigAttribute ;
        $adminUserConfigAttribute = $this->adminUserConfigAttribute ;
        
        $result = $voter->vote($this->authentication, null, array());
        $this->assertEquals(AccessDecisionVoter::ACCESS_ABSTAIN, $result);
        
        //hasRole('ROLE_ADMIN')
        $result = $voter->vote($this->authentication, null, array($adminConfigAttribute));
        $this->assertEquals(AccessDecisionVoter::ACCESS_GRANTED, $result);
        
        //hasRole('ROLE_USER')
        $result = $voter->vote($this->authentication, null, array($userConfigAttribute));
        $this->assertEquals(AccessDecisionVoter::ACCESS_GRANTED, $result);
        
        //hasRole('ROLE_TEST')
        $result = $voter->vote($this->authentication, null, array($testConfigAttribute));
        $this->assertEquals(AccessDecisionVoter::ACCESS_DENIED, $result);
        
        //hasAnyRole('ROLE_ADMIN', 'ROLE_USER')
        $result = $voter->vote($this->authentication, null, array($adminUserConfigAttribute));
        $this->assertEquals(AccessDecisionVoter::ACCESS_GRANTED, $result);
        
        //hasRole('ROLE_ADMIN') OR hasRole('ROLE_USER')
        $result = $voter->vote($this->authentication, null, array($adminConfigAttribute, $userConfigAttribute));
        $this->assertEquals(AccessDecisionVoter::ACCESS_GRANTED, $result);
        
        //hasRole('ROLE_ADMIN') OR hasRole('ROLE_TEST')
        $result = $voter->vote($this->authentication, null, array($adminConfigAttribute, $testConfigAttribute));
        $this->assertEquals(AccessDecisionVoter::ACCESS_GRANTED, $result);
    }
    
    public function processDecisionManager($decisionManager) {
        $adminConfigAttribute = $this->adminConfigAttribute;
        $userConfigAttribute = $this->userConfigAttribute ;
        $testConfigAttribute = $this->testConfigAttribute ;
        $adminUserConfigAttribute = $this->adminUserConfigAttribute ;
        
        try {
            $decisionManager->decide($this->authentication, null, array());
            $this->assertTrue(false, 'Empty conditions');
        } catch (Exception $e) {
            $this->assertTrue(true, 'Empty conditions');
        }
        
        //hasRole('ROLE_ADMIN')
        try {
            $decisionManager->decide($this->authentication, null, array($adminConfigAttribute));
            $this->assertTrue(true, 'ROLE_ADMIN');
        } catch (Exception $e) {
            $this->assertTrue(false, 'ROLE_ADMIN');
        }
        
        //hasRole('ROLE_USER')
        try {
            $decisionManager->decide($this->authentication, null, array($userConfigAttribute));
            $this->assertTrue(true, 'ROLE_USER');
        } catch (Exception $e) {
            $this->assertTrue(false, 'ROLE_USER');
        }
        
        //hasRole('ROLE_TEST')
        try {
            $decisionManager->decide($this->authentication, null, array($testConfigAttribute));
            $this->assertTrue(false, 'ROLE_TEST');
        } catch (Exception $e) {
            $this->assertTrue(true, 'ROLE_TEST');
        }
        
        //hasAnyRole('ROLE_ADMIN', 'ROLE_USER')
        try {
            $decisionManager->decide($this->authentication, null, array($adminUserConfigAttribute));
            $this->assertTrue(true, 'ROLE_ADMIN or ROLE_USER');
        } catch (Exception $e) {
            $this->assertTrue(false, 'ROLE_ADMIN or ROLE_USER');
        }
    }
    
    public function testUnanimousDecisionManager() {
        $adminConfigAttribute = $this->adminConfigAttribute;
        $userConfigAttribute = $this->userConfigAttribute ;
        $testConfigAttribute = $this->testConfigAttribute ;
        $adminUserConfigAttribute = $this->adminUserConfigAttribute ;
        
        $decisionManager = new UnanimousBased(array($this->voter));
        $this->processDecisionManager($decisionManager);
        
        //hasRole('ROLE_ADMIN') AND hasRole('ROLE_USER')
        try {
            $decisionManager->decide($this->authentication, null, array($adminConfigAttribute, $userConfigAttribute));
            $this->assertTrue(true, 'ROLE_USER AND ROLE_ADMIN');
        } catch (Exception $e) {
            echo $e->getTraceAsString();
            $this->assertTrue(false, 'ROLE_USER AND ROLE_ADMIN');
        }
        
        //hasRole('ROLE_ADMIN') AND hasRole('ROLE_USER')
        try {
            $decisionManager->decide($this->authentication, null, array($adminConfigAttribute, $testConfigAttribute));
            $this->assertTrue(false, 'ROLE_TEST AND ROLE_ADMIN');
        } catch (Exception $e) {
            $this->assertTrue(true, 'ROLE_TEST AND ROLE_ADMIN');
        }
    }
    
    public function testConsensusDecisionManager() {
        $adminConfigAttribute = $this->adminConfigAttribute;
        $userConfigAttribute = $this->userConfigAttribute ;
        $testConfigAttribute = $this->testConfigAttribute ;
        $adminUserConfigAttribute = $this->adminUserConfigAttribute ;
        
        $decisionManager = new ConsensusBased(array($this->voter));
        $this->processDecisionManager($decisionManager);
        
        //hasRole('ROLE_ADMIN') AND hasRole('ROLE_USER')
        try {
            $decisionManager->decide($this->authentication, null, array($adminConfigAttribute, $userConfigAttribute));
            $this->assertTrue(true, 'ROLE_USER AND ROLE_ADMIN');
        } catch (Exception $e) {
            $this->assertTrue(false, 'ROLE_USER AND ROLE_ADMIN');
        }
        
        //hasRole('ROLE_ADMIN') AND hasRole('ROLE_USER')
        try {
            $decisionManager->decide($this->authentication, null, array($adminConfigAttribute, $testConfigAttribute));
            $this->assertTrue(false, 'ROLE_TEST AND ROLE_ADMIN');
        } catch (Exception $e) {
            $this->assertTrue(true, 'ROLE_TEST AND ROLE_ADMIN');
        }
    }
    
    public function testAffirmativeDecisionManager() {
        $adminConfigAttribute = $this->adminConfigAttribute;
        $userConfigAttribute = $this->userConfigAttribute ;
        $testConfigAttribute = $this->testConfigAttribute ;
        $adminUserConfigAttribute = $this->adminUserConfigAttribute ;
        
        $decisionManager = new AffirmativeBased(array($this->voter));
        $this->processDecisionManager($decisionManager);
        
        //hasRole('ROLE_ADMIN') OR hasRole('ROLE_USER')
        try {
            $decisionManager->decide($this->authentication, null, array($adminConfigAttribute, $userConfigAttribute));
            $this->assertTrue(true, 'ROLE_USER AND ROLE_ADMIN');
        } catch (Exception $e) {
            $this->assertTrue(false, 'ROLE_USER AND ROLE_ADMIN');
        }
        
        //hasRole('ROLE_ADMIN') OR hasRole('ROLE_USER')
        try {
            $decisionManager->decide($this->authentication, null, array($adminConfigAttribute, $testConfigAttribute));
            $this->assertTrue(true, 'ROLE_TEST AND ROLE_ADMIN');
        } catch (Exception $e) {
            $this->assertTrue(false, 'ROLE_TEST AND ROLE_ADMIN');
        }
    }
    
    public function nestedCall($toto, $titi, $config) {
        $securityContext = new SimpleSecurityContext(new UnanimousBased(array($this->voter)));
        $securityContext->setAuthentication($this->authentication);
        $secured = new Secured($securityContext, $config);
        $secured->preAuthorize('nestedCall');
    }
    
    public function testSecuredSuccess() {
        $config = array(
            'nestedCall'=>array(
                Secured::PRE_AUTHORIZE => array(
                    $this->adminConfigAttribute
                )    
            )    
        );
        $this->nestedCall('toto', 'titi', $config);
        $this->assertTrue(true);
    }
    
    public function testSecuredFailure() {
        $config = array(
            'nestedCall'=>array(
                Secured::PRE_AUTHORIZE => array(
                    $this->testConfigAttribute
                )
            )
        );
        try {
            $this->nestedCall('toto', 'titi', $config);
            $this->assertTrue(false);
        } catch (\exception $e){
            $this->assertTrue(true);
        }
    }

    public function testSecurityContext (){
        $securityContext = new SimpleSecurityContext(new UnanimousBased(array($this->voter)));
        $securityContext->setAuthentication($this->authentication);

        $this->assertTrue ($securityContext->hasRole ('ROLE_ADMIN'));
        $this->assertFalse ($securityContext->hasRole ('ROLE_TEST'));
        $this->assertFalse ($securityContext->hasAnyRole (array ('ROLE_TEST')));
        $this->assertTrue ($securityContext->hasAnyRole (array ('ROLE_TEST', 'ROLE_ADMIN')));
    }
}