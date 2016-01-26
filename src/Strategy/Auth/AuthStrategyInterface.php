<?php

namespace Eljam\GuzzleJwt\Strategy\Auth;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
interface AuthStrategyInterface
{
    /**
     * getRequestOptions.
     *
     * @return array
     */
    public function getRequestOptions();
}
