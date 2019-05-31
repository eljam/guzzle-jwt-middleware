<?php

namespace Eljam\GuzzleJwt\Tests\Persistence;

use Eljam\GuzzleJwt\JwtToken;
use Eljam\GuzzleJwt\Persistence\NullTokenPersistence;

/**
 * @author Nicolas Reynis (nreynis)
 */
class TokenPersistenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testNullTokenPersistence.
     */
    public function testNullTokenPersistence()
    {
        $tokenPersistence = new NullTokenPersistence();
        $token = new JwtToken('foo', new \DateTime('now'));

        $tokenPersistence->saveToken($token);

        $this->assertFalse($tokenPersistence->hasToken());
        $this->assertNull($tokenPersistence->restoreToken());
    }
}
