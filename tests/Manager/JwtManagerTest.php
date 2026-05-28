<?php

namespace Eljam\GuzzleJwt\Tests\Manager;

use Eljam\GuzzleJwt\JwtToken;
use Eljam\GuzzleJwt\Manager\JwtManager;
use Eljam\GuzzleJwt\Strategy\Auth\QueryAuthStrategy;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

/**
 * @author Guillaume Cavavana <guillaume.cavana@gmail.com>
 */
class JwtManagerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * testGetTokenExpiredKeyException
     */
    public function testGetTokenExpiredKeyException()
    {
        $mockHandler = new MockHandler([
            function (RequestInterface $request) {

                $this->assertTrue($request->hasHeader('timeout'));
                $this->assertEquals(
                    3,
                    $request->getHeaderLine('timeout')
                );

                $jsonPayload = <<<EOF
            {
                "status": "success",
                "message": "Login successful",
                "payload": {
                    "token": "1453720507"
                },
                "expires_in": 3600
            }
EOF;

                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    $jsonPayload
                );
            },
        ]);

        $handler = HandlerStack::create($mockHandler);

        $authClient = new Client([
            'handler' => $handler,
        ]);

        $authStrategy = new QueryAuthStrategy(['username' => 'admin', 'password' => 'admin']);

        $jwtManager = new JwtManager(
            $authClient,
            $authStrategy,
            null,
            [
                'token_url' => '/api/token',
                'timeout' => 3,
                'token_key' => 'token',
                'expire_key' => 'expires_in'
            ]
        );

        $this->expectException('Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException');

        $token = $jwtManager->getJwtToken();
    }

    /**
     * testGetTokenWithSublevelResponse
     */
    public function testGetTokenWithSublevelResponse()
    {
        $mockHandler = new MockHandler([
            function (RequestInterface $request) {

                $this->assertTrue($request->hasHeader('timeout'));
                $this->assertEquals(
                    3,
                    $request->getHeaderLine('timeout')
                );

                $jsonPayload = <<<EOF
            {
                "status": "success",
                "message": "Login successful",
                "payload": {
                    "token": "1453720507"
                },
                "expires_in": 3600
            }
EOF;

                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    $jsonPayload
                );
            },
        ]);

        $handler = HandlerStack::create($mockHandler);

        $authClient = new Client([
            'handler' => $handler,
        ]);

        $authStrategy = new QueryAuthStrategy(['username' => 'admin', 'password' => 'admin']);

        $jwtManager = new JwtManager(
            $authClient,
            $authStrategy,
            null,
            [
                'token_url' => '/api/token',
                'timeout' => 3,
                'token_key' => 'payload.token',
                'expire_key' => 'expires_in'
            ]
        );
        $token = $jwtManager->getJwtToken();

        $this->assertInstanceOf(JwtToken::class, $token);
        $this->assertEquals('1453720507', $token->getToken());
    }

    /**
     * testGetToken.
     */
    public function testGetToken()
    {
        $mockHandler = new MockHandler([
            function (RequestInterface $request) {

                $this->assertTrue($request->hasHeader('timeout'));
                $this->assertEquals(
                    3,
                    $request->getHeaderLine('timeout')
                );

                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode(['token' => '1453720507', 'expires_in' => 3600])
                );
            },
        ]);

        $handler = HandlerStack::create($mockHandler);

        $authClient = new Client([
            'handler' => $handler,
        ]);

        $authStrategy = new QueryAuthStrategy(['username' => 'admin', 'password' => 'admin']);

        $jwtManager = new JwtManager(
            $authClient,
            $authStrategy,
            null,
            ['token_url' => '/api/token', 'timeout' => 3]
        );
        $token = $jwtManager->getJwtToken();

        $this->assertInstanceOf(JwtToken::class, $token);
        $this->assertEquals('1453720507', $token->getToken());
    }

    public function testGetTokenWithTokenKeyOption()
    {
        $mockHandler = new MockHandler([
            function (RequestInterface $request) {

                $this->assertTrue($request->hasHeader('timeout'));
                $this->assertEquals(
                    3,
                    $request->getHeaderLine('timeout')
                );

                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode(['tokenkey' => '1453720507'])
                );
            },
        ]);

        $handler = HandlerStack::create($mockHandler);

        $authClient = new Client([
            'handler' => $handler,
        ]);

        $authStrategy = new QueryAuthStrategy(['username' => 'admin', 'password' => 'admin']);

        $jwtManager = new JwtManager(
            $authClient,
            $authStrategy,
            null,
            ['token_url' => '/api/token', 'timeout' => 3, 'token_key' => 'tokenkey']
        );
        $token = $jwtManager->getJwtToken();

        $this->assertInstanceOf(JwtToken::class, $token);
        $this->assertEquals('1453720507', $token->getToken());
    }

    public function testGetTokenWithNullPropertyAccessor()
    {
        $mockHandler = new MockHandler([
            function (RequestInterface $request) {

                $this->assertTrue($request->hasHeader('timeout'));
                $this->assertEquals(
                    3,
                    $request->getHeaderLine('timeout')
                );

                return new Response(
                    200,
                    ['Content-Type' => 'text/plain'],
                    '1453720507' // plain text !
                );
            },
        ]);

        $handler = HandlerStack::create($mockHandler);

        $authClient = new Client([
            'handler' => $handler,
        ]);

        $authStrategy = new QueryAuthStrategy(['username' => 'admin', 'password' => 'admin']);

        $jwtManager = new JwtManager(
            $authClient,
            $authStrategy,
            null,
            ['token_url' => '/api/token', 'timeout' => 3] // no (useless) 'token_key' option
        );
        $jwtManager->setPropertyAccessor(null);

        $token = $jwtManager->getJwtToken();

        $this->assertInstanceOf(JwtToken::class, $token);
        $this->assertEquals('1453720507', $token->getToken());
    }

    public function testGetTokenShouldGetNewTokenIfCachedTokenIsNotValid()
    {
        $mockHandler = new MockHandler(
            [
                function (RequestInterface $request) {

                    $this->assertTrue($request->hasHeader('timeout'));
                    $this->assertEquals(
                        3,
                        $request->getHeaderLine('timeout')
                    );

                    return new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        json_encode(['token' => '1453720507'])
                    );
                },
                function (RequestInterface $request) {

                    $this->assertTrue($request->hasHeader('timeout'));
                    $this->assertEquals(
                        3,
                        $request->getHeaderLine('timeout')
                    );

                    return new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        json_encode(['token' => 'foo123'])
                    );
                },
            ]
        );

        $handler = HandlerStack::create($mockHandler);

        $authClient = new Client([
            'handler' => $handler,
        ]);

        $authStrategy = new QueryAuthStrategy(['username' => 'admin', 'password' => 'admin']);

        $jwtManager = new JwtManager(
            $authClient,
            $authStrategy,
            null,
            ['token_url' => '/api/token', 'timeout' => 3]
        );
        $token = $jwtManager->getJwtToken();

        $this->assertInstanceOf(JwtToken::class, $token);
        $this->assertEquals('1453720507', $token->getToken());

        $token = $jwtManager->getJwtToken();

        $this->assertInstanceOf(JwtToken::class, $token);
        $this->assertEquals('foo123', $token->getToken());
    }

    public function testGetTokenShouldUseTheCachedTokenIfItIsValid()
    {
        $mockHandler = new MockHandler(
            [
                function (RequestInterface $request) {

                    $this->assertTrue($request->hasHeader('timeout'));
                    $this->assertEquals(
                        3,
                        $request->getHeaderLine('timeout')
                    );

                    return new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        json_encode(['token' => '1453720507', 'expires_in' => 3600])
                    );
                },
                function (RequestInterface $request) {

                    $this->assertTrue($request->hasHeader('timeout'));
                    $this->assertEquals(
                        3,
                        $request->getHeaderLine('timeout')
                    );

                    return new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        json_encode(['token' => 'foo123'])
                    );
                },
            ]
        );

        $handler = HandlerStack::create($mockHandler);

        $authClient = new Client([
            'handler' => $handler,
        ]);

        $authStrategy = new QueryAuthStrategy(['username' => 'admin', 'password' => 'admin']);

        $jwtManager = new JwtManager(
            $authClient,
            $authStrategy,
            null,
            ['token_url' => '/api/token', 'timeout' => 3]
        );
        $token = $jwtManager->getJwtToken();

        $this->assertInstanceOf(JwtToken::class, $token);
        $this->assertEquals('1453720507', $token->getToken());

        $token = $jwtManager->getJwtToken();

        $this->assertInstanceOf(JwtToken::class, $token);
        $this->assertEquals('1453720507', $token->getToken());
    }

    public function testGetTokenShouldUseTheCachedTokenIfItIsValidBasedOnExpField()
    {
        $jwtToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9'
            . '.eyJleHAiOiIzMjUwMzY4MDAwMCJ9'
            . '.k4YJmJooaa9B4pAM_U8Pi-4ss6RdKFtj9iQqLIAndVA';

        $mockHandler = new MockHandler(
            [
                function (RequestInterface $request) use ($jwtToken) {

                    $this->assertTrue($request->hasHeader('timeout'));
                    $this->assertEquals(
                        3,
                        $request->getHeaderLine('timeout')
                    );

                    return new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        json_encode(['token' => $jwtToken])
                    );
                },
                function (RequestInterface $request) {

                    $this->assertTrue($request->hasHeader('timeout'));
                    $this->assertEquals(
                        3,
                        $request->getHeaderLine('timeout')
                    );

                    return new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        json_encode(['token' => uniqid('token', true)])
                    );
                },
            ]
        );

        $handler = HandlerStack::create($mockHandler);

        $authClient = new Client([
            'handler' => $handler,
        ]);

        $authStrategy = new QueryAuthStrategy(['username' => 'admin', 'password' => 'admin']);

        $jwtManager = new JwtManager(
            $authClient,
            $authStrategy,
            null,
            ['token_url' => '/api/token', 'timeout' => 3]
        );
        $token = $jwtManager->getJwtToken();

        $this->assertInstanceOf(JwtToken::class, $token);
        $this->assertEquals($jwtToken, $token->getToken());

        $token = $jwtManager->getJwtToken();

        $this->assertInstanceOf(JwtToken::class, $token);
        $this->assertEquals($jwtToken, $token->getToken());
    }
}
