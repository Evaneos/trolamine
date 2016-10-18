<?php

namespace Trolamine\Core\Authentication;


class JsonWebTokenAuthentication extends AbstractAuthentication
{
    /** @var string  */
    private $jwt;

    /**
     * JsonWebTokenAuthentication constructor.
     *
     * @param UserDetails|null $user
     * @param string           $jwt
     * @param array            $mode
     * @param array            $authorities
     */
    public function __construct(
        UserDetails $user = null,
        $jwt,
        $mode,
        array $authorities = []
    ) {
        parent::__construct($user, $mode, $authorities);
        if ($user instanceof UserDetails) {
            $this->principal = $user->getUsername();
        }

        $this->jwt = $jwt;
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        //We never return the password in this context
        return $this->jwt;
    }

    /**
     * @param array $authorities
     */
    public function setAuthorities(array $authorities)
    {
        $this->authorities = $authorities;
    }
}
