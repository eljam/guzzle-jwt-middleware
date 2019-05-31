<?php

namespace Eljam\GuzzleJwt\Tests\Persistence;

use Eljam\GuzzleJwt\JwtToken;
use Eljam\GuzzleJwt\Persistence\NullTokenPersistence;
use Eljam\GuzzleJwt\Persistence\SimpleCacheTokenPersistence;
use Symfony\Component\Cache\Simple\FilesystemCache;

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

    /**
     * testSimpleCacheTokenPersistence.
     */
    public function testSimpleCacheTokenPersistence()
    {
        $simpleCache = new FilesystemCache();
        $tokenPersistence = new SimpleCacheTokenPersistence($simpleCache);
        $token = new JwtToken('foo', new \DateTime('now'));

        $tokenPersistence->saveToken($token);

        $this->assertTrue($tokenPersistence->hasToken());
        $this->assertEquals($tokenPersistence->restoreToken()->getToken(), $token->getToken());

        $tokenPersistence->deleteToken();

        $this->assertFalse($tokenPersistence->hasToken());
        $this->assertNull($tokenPersistence->restoreToken());
    }
}
