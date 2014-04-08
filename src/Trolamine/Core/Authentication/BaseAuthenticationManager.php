<?php
namespace Trolamine\Core\Authentication;

use Trolamine\Core\Exception\DisabledException;
use Trolamine\Core\Exception\LockedException;
use Trolamine\Core\Authentication\Password\PasswordEncoder;
use Trolamine\Core\Exception\BadCredentialsException;
use Trolamine\Core\Authentication\Role\RoleManager;

class BaseAuthenticationManager implements AuthenticationManager {
    
    /**
     * 
     * @var UserDetailsService
     */
    protected $userDetailsService;
    
    /**
     *
     * @var PasswordEncoder
     */
    protected $passwordEncoder;
    
    /**
     *
     * @var RoleManager
     */
    protected $roleManager;
    
    /**
     * Constructor
     * 
     * @param UserDetailsService $userDetailsService
     */
    public function __construct(
            UserDetailsService $userDetailsService=null,
            PasswordEncoder $passwordEncoder=null,
            RoleManager $roleManager=null) {
        $this->userDetailsService =  $userDetailsService;
        $this->passwordEncoder = $passwordEncoder;
        $this->roleManager = $roleManager;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\AuthenticationManager::authenticate()
     */
    function authenticate(Authentication $authentication) {
        if ($this->userDetailsService == null) {
            throw \RuntimeException('You have to declare a UserDetailsService.');
        }
        
        $userDetails = $this->userDetailsService->loadUserByUsername($authentication->getPrincipal());
        
        if (!$userDetails->isAccountNonExpired() || !$userDetails->isCredentialsNonExpired() || !$userDetails->isEnabled()) {
            throw new DisabledException();
        }
        
        if (!$userDetails->isAccountNonLocked()) {
            throw new LockedException();
        }
        
        $credentials = $authentication->getCredentials();
        if ($this->passwordEncoder != null) {
            $credentials = $this->passwordEncoder->encodePassword($credentials);
        }
        
        if ($userDetails->getPassword() != $credentials) {
            throw new BadCredentialsException();
        }
        
        $roles = array();
        if ($this->roleManager != null) {
            $roles = $this->roleManager->getRoles($userDetails);
        }
        
        return new BaseAuthentication($userDetails, Authentication::FULLY_AUTHENTICATED, $roles);
    }
    
    /**
     * 
     * @param UserDetailsService $userDetailsService
     */
    public function setUserDetailsService(UserDetailsService $userDetailsService) {
        $this->userDetailsService =  $userDetailsService;
    }
    
    /**
     * 
     * @param PasswordEncoder $passwordEncoder
     */
    public function setPasswordEncoder(PasswordEncoder $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    /**
     * 
     * @param RoleManager $roleManager
     */
    public function setRoleManager(RoleManager $roleManager) {
        $this->roleManager = $roleManager;
    }
}
