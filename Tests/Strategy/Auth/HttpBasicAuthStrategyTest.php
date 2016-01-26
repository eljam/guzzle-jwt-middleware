<?php

namespace Eljam\GuzzleJwt\Tests\Strategy\Auth;

use Eljam\GuzzleJwt\Strategy\Auth\HttpBasicAuthStrategy;

/**
 * @author Guillaume Cavavana <guillaume.cavana@gmail.com>
 */
class HttpBasicAuthStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testGetToken.
     */
    public function testGetToken()
    {
        $authStrategy = new HttpBasicAuthStrategy(
            [
                'username' => 'username',
                'password' => 'password',
            ]
        );

        $this->assertEquals('username', $authStrategy->getGuzzleRequestOptions()['auth'][0]);
        $this->assertEquals('password', $authStrategy->getGuzzleRequestOptions()['auth'][1]);
    }
}
