<?php

namespace Eljam\GuzzleJwt\Tests\Manager;

use Eljam\GuzzleJwt\JwtToken;
use Eljam\GuzzleJwt\Manager\JwtManager;
use Eljam\GuzzleJwt\Strategy\Auth\QueryAuthStrategy;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

/**
 * @author Guillaume Cavavana <guillaume.cavana@gmail.com>
 */
class JwtManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testGetToken.
     */
    public function testGetToken()
    {
        $mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode(['token' => '1453720507'])
            ), ]
        );

        $handler = HandlerStack::create($mock);

        $authClient = new Client([
            'handler' => $handler,
        ]);

        $authStrategy = new QueryAuthStrategy(['username' => 'admin', 'password' => 'admin']);

        $jwtManager = new JwtManager($authClient, $authStrategy);
        $token = $jwtManager->getJwtToken();

        $this->assertInstanceOf(JwtToken::class, $token);
        $this->assertEquals('1453720507', $token->getToken());
    }
}
