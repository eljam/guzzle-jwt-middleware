<?php

namespace Eljam\GuzzleJwt\Strategy\Auth;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
interface AuthStrategyInterface
{
    /**
     * getGuzzleRequestOptions.
     *
     * @return array
     */
    public function getGuzzleRequestOptions();
}
