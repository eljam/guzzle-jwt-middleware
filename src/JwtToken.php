<?php

namespace Eljam\GuzzleJwt;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
class JwtToken
{
    /**
     * $token.
     *
     * @var string
     */
    private $token;

    /**
     * @var \DateTime
     */
    private $expiration;

    /**
     * Constructor.
     *
     * @param string    $token
     * @param \DateTime $expiration
     */
    public function __construct($token, \DateTime $expiration = null)
    {
        $this->token = $token;
        $this->expiration = $expiration;
    }

    /**
     * getToken.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if (!$this->expiration) {
            return false;
        }

        return (new \DateTime()) < $this->expiration;
    }
}
