<?php

namespace Eljam\GuzzleJwt\Tests;

use Eljam\GuzzleJwt\JwtMiddleware;
use Eljam\GuzzleJwt\Manager\JwtManager;
use Eljam\GuzzleJwt\Strategy\Auth\HttpBasicAuthStrategy;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

/**
 * @author Guillaume Cavavana <guillaume.cavana@gmail.com>
 */
class JwtMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testJwtAuthorizationHeader.
     */
    public function testJwtAuthorizationHeader()
    {
        $authMockHandler = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode(['token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9'])
            ),
        ]);

        $authClient = new Client(['handler' => $authMockHandler]);
        $jwtManager = new JwtManager(
            $authClient,
            (new HttpBasicAuthStrategy(['username' => 'test', 'password' => 'test']))
        );

        $mockHandler = new MockHandler([
            function (RequestInterface $request) {
                $this->assertTrue($request->hasHeader('Authorization'));
                $this->assertSame(
                    'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9',
                    $request->getHeader('Authorization')[0]
                );

                return new Response(200, [], json_encode(['data' => 'pong']));
            },
        ]);

        $handler = HandlerStack::create($mockHandler);
        $handler->push(new JwtMiddleware($jwtManager));

        $client = new Client(['handler' => $handler]);
        $client->get('http://api.example.com/api/ping');
    }

    /**
     * testJwtAuthorizationHeaderType.
     */
    public function testJwtAuthorizationHeaderType()
    {
        $authMockHandler = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode(['token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9'])
            ),
        ]);

        $authClient = new Client(['handler' => $authMockHandler]);
        $jwtManager = new JwtManager(
            $authClient,
            (new HttpBasicAuthStrategy(['username' => 'test', 'password' => 'test']))
        );

        $mockHandler = new MockHandler([
            function (RequestInterface $request) {
                $this->assertTrue($request->hasHeader('Authorization'));
                $this->assertSame(
                    'JWT eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9',
                    $request->getHeader('Authorization')[0]
                );

                return new Response(200, [], json_encode(['data' => 'pong']));
            },
        ]);

        $handler = HandlerStack::create($mockHandler);
        $handler->push(new JwtMiddleware($jwtManager, 'JWT'));

        $client = new Client(['handler' => $handler]);
        $client->get('http://api.example.com/api/ping');
    }
}
