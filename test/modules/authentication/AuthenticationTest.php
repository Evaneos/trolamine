<?php

use Trolamine\Core\Authentication\User;
use Trolamine\Core\Authentication\Password\MD5Encoder;
use Trolamine\Core\Authentication\UserDetailsService;
use Trolamine\Core\Authentication\BaseAuthenticationManager;
use Trolamine\Core\Authentication\UsernamePasswordAuthenticationToken;
class AuthenticationTest extends PHPUnit_Framework_TestCase {
    
    private $passwordEncoder;
    
    public function setUp() {
        $this->passwordEncoder = new MD5Encoder();
    }
    
    protected function getUserDetailServiceMock($userDetails) {
        /* @var $userDetailsService UserDetailsService */
        $userDetailsService = $this->getMock('Trolamine\\Core\\Authentication\\UserDetailsService');
        $userDetailsService->expects($this->any())
            ->method('loadUserByUsername')
            ->willReturn($userDetails);
        
        return $userDetailsService;
    }
    
    protected function getRoleManagerMock($roles) {
        $roleManager = $this->getMock('Trolamine\\Core\\Authentication\\Role\\RoleManager');
        $roleManager->expects($this->any())
            ->method('getRoles')
            ->willReturn($roles);
        
        return $roleManager;
    }
    
    public function testAuthentication() {
        
        $authenticationToken = new UsernamePasswordAuthenticationToken('test', 'password');
        
        $userDetailsService = $this->getUserDetailServiceMock(
            new User(
                'test',
                $this->passwordEncoder->encodePassword('password')
            )
        );
        
        $roles = array('ROLE_USER', 'ROLE_ADMIN');
        $roleManager = $this->getRoleManagerMock($roles);
        
        $authenticationManager = new BaseAuthenticationManager($userDetailsService, $this->passwordEncoder, $roleManager);
        $authentication = $authenticationManager->authenticate($authenticationToken);
        
        $this->assertEquals($authenticationToken->getPrincipal(), $authentication->getPrincipal());
        $this->assertEquals($roles, $authentication->getAuthorities());
    }
    
    public function authenticationFailure($userDetails, $exceptionType) {
    
        $userDetailsService = $this->getUserDetailServiceMock($userDetails);
        
        $this->setExpectedException($exceptionType);
    
        $authenticationManager = new BaseAuthenticationManager($userDetailsService, $this->passwordEncoder);
        $authenticationManager->authenticate(new UsernamePasswordAuthenticationToken('test', 'password'));
    }
    
    public function testAuthenticationFailureDisabled() {
        $userDetails = new User(
            'test',
            $this->passwordEncoder->encodePassword('password'),
            true,
            true,
            true,
            false //disabled
        );
        
        $this->authenticationFailure($userDetails, '\\Trolamine\\Core\\Exception\\DisabledException');
    }
    
    public function testAuthenticationFailureAccountExpired() {
        $userDetails = new User(
                'test',
                $this->passwordEncoder->encodePassword('password'),
                false, //account expired
                true,
                true,
                true
        );
        $this->authenticationFailure($userDetails, '\\Trolamine\\Core\\Exception\\DisabledException');
    }
    
    public function testAuthenticationFailureCredentialsExpired() {
        $userDetails = new User(
                'test',
                $this->passwordEncoder->encodePassword('password'),
                true,
                true,
                false, //credentials expired
                true
        );
        $this->authenticationFailure($userDetails, '\\Trolamine\\Core\\Exception\\DisabledException');
    }
    
    public function testAuthenticationFailureLocked() {
        $userDetails = new User(
                'test',
                $this->passwordEncoder->encodePassword('password'),
                true,
                false, //locked
                true,
                true
        );
    
        
        $this->authenticationFailure($userDetails, '\\Trolamine\\Core\\Exception\\LockedException');
    }
    
    public function testAuthenticationFailureBadCredentials() {
        $userDetails = new User('test', $this->passwordEncoder->encodePassword('pwd'));
        $this->authenticationFailure($userDetails, '\\Trolamine\\Core\\Exception\\BadCredentialsException');
    }
}