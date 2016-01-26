<?php

namespace Eljam\GuzzleJwt\Strategy\Auth;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
class HttpBasicAuthStrategy extends AbstractBaseAuthStrategy
{
    /**
     * {@inheritdoc}
     */
    public function getRequestOptions()
    {
        return [
            \GuzzleHttp\RequestOptions::AUTH => [
                $this->options['username'],
                $this->options['password'],
            ],
        ];
    }
}
