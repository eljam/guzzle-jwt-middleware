<?php

namespace Eljam\GuzzleJwt\Tests\Strategy\Auth;

use Eljam\GuzzleJwt\Strategy\Auth\QueryAuthStrategy;

/**
 * @author Guillaume Cavavana <guillaume.cavana@gmail.com>
 */
class QueryAuthStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testGetToken.
     */
    public function testGetToken()
    {
        $authStrategy = new QueryAuthStrategy(
            [
                'username' => 'admin',
                'password' => 'admin',
                'query_fields' => ['username', 'password'],
            ]
        );

        $this->assertTrue(array_key_exists('username', $authStrategy->getGuzzleRequestOptions()['query']));
        $this->assertTrue(array_key_exists('password', $authStrategy->getGuzzleRequestOptions()['query']));
    }
}
