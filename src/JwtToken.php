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
    protected $token;

    /**
     * Constructor.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
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
}
