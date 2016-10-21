<?php

namespace Trolamine\Core\Authentication;


class JsonWebTokenAuthentication extends AbstractAuthentication
{
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

        $this->credentials = $jwt;
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @param array $authorities
     */
    public function setAuthorities(array $authorities)
    {
        $this->authorities = $authorities;
    }
}
