<?php
namespace Trolamine\Core\Operation;

use \Trolamine\Core\Permission\Permission;
use \Trolamine\Core\Permission\PermissionEvaluator;
use \Trolamine\Core\Authentication\Authentication;

abstract class AbstractOperation implements Operation {

    /**
     * The authentication object
     *
     * @var Authentication
     */
    protected $authentication;

    public function __construct(Authentication $a=null) {
        $this->authentication =$a;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\Operation::setAuthentication()
     */
    public function setAuthentication(Authentication $authentication) {
        $this->authentication =$authentication;
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Operation\Operation::getAuthentication()
     */
    public function getAuthentication(){
        return $this->authentication;
    }

}
