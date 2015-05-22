<?php
namespace Trolamine\Core\Exception;

use Trolamine\Core\Authentication\Authentication;

class AccessDeniedException extends \Exception
{
    /**
     * @var Authentication
     */
    private $authentication;

    /**
     * @return Authentication
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @param Authentication $authentication
     * @return self
     */
    public function setAuthentication($authentication)
    {
        $this->authentication = $authentication;
        return $this;
    }
}
