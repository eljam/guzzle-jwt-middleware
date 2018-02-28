<?php

namespace Eljam\GuzzleJwt;

use Eljam\GuzzleJwt\Manager\JwtManager;
use Psr\Http\Message\RequestInterface;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
class JwtMiddleware
{
    /**
     * $JwtManager.
     *
     * @var JwtManager
     */
    protected $jwtManager;

    /**
     * The Authorization Header Type (defaults to Bearer)
     *
     * @var string
     */
    protected $authorizationHeaderType;

    /**
     * Constructor.
     *
     * @param JwtManager $jwtManager
     * @param string $authorizationHeaderType
     */
    public function __construct(JwtManager $jwtManager, $authorizationHeaderType = 'Bearer')
    {
        $this->jwtManager = $jwtManager;
        $this->authorizationHeaderType = $authorizationHeaderType;
    }

    /**
     * Called when the middleware is handled by the client.
     *
     * @param callable $handler
     *
     * @return callable
     */
    public function __invoke(callable $handler)
    {
        $manager = $this->jwtManager;

        return function (
            RequestInterface $request,
            array $options
        ) use (
            $handler,
            $manager
        ) {
            $token = $manager->getJwtToken()->getToken();

            return $handler($request->withHeader(
                'Authorization',
                sprintf('%s %s', $this->authorizationHeaderType, $token)
            ), $options);
        };
    }
}
