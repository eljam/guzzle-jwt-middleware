<?php

namespace Eljam\GuzzleJwt\Manager;

use Eljam\GuzzleJwt\JwtToken;
use Eljam\GuzzleJwt\Persistence\NullTokenPersistence;
use Eljam\GuzzleJwt\Persistence\TokenPersistenceInterface;
use Eljam\GuzzleJwt\Strategy\Auth\AuthStrategyInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
class JwtManager
{
    /**
     * $client Guzzle Client.
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * $auth Authentication Strategy.
     *
     * @var AuthStrategyInterface
     */
    protected $auth;

    /**
     * $options.
     *
     * @var array
     */
    protected $options;

    /**
     * $token.
     *
     * @var JwtToken
     */
    protected $token;

    /**
     * @var TokenPersistenceInterface
     */
    protected $tokenPersistence;

    /**
     * Constructor.
     *
     * @param ClientInterface           $client
     * @param AuthStrategyInterface     $auth
     * @param TokenPersistenceInterface $tokenPersistence
     * @param array                     $options
     */
    public function __construct(
        ClientInterface $client,
        AuthStrategyInterface $auth,
        TokenPersistenceInterface $tokenPersistence = null,
        array $options = []
    ) {
        $this->client = $client;
        $this->auth = $auth;

        if ($tokenPersistence === null) {
            $tokenPersistence = new NullTokenPersistence();
        }
        $this->tokenPersistence = $tokenPersistence;

        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'token_url' => '/token',
            'timeout' => 1,
            'token_key' => 'token',
            'expire_key' => 'expires_in',
        ]);

        $resolver->setRequired(['token_url', 'timeout']);

        $this->options = $resolver->resolve($options);
    }

    /**
     * getToken.
     *
     * @return JwtToken
     */
    public function getJwtToken()
    {
        // If token is not set try to get it from the persistent storage.
        if ($this->token === null) {
            $this->token = $this->tokenPersistence->restoreToken();
        }

        if ($this->token && $this->token->isValid()) {
            return $this->token;
        }

        $this->tokenPersistence->deleteToken();

        $url = $this->options['token_url'];

        $requestOptions = array_merge(
            $this->getDefaultHeaders(),
            $this->auth->getRequestOptions()
        );

        $response = $this->client->request('POST', $url, $requestOptions);
        $body = json_decode($response->getBody(), true);

        $expiresIn = isset($body[$this->options['expire_key']]) ? $body[$this->options['expire_key']] : null;

        if ($expiresIn) {
            $expiration = new \DateTime('now + ' . $expiresIn . ' seconds');
        } elseif (count($jwtParts = explode('.', $body[$this->options['token_key']])) === 3
            && is_array($payload = json_decode(base64_decode($jwtParts[1]), true))
            // https://tools.ietf.org/html/rfc7519.html#section-4.1.4
            && array_key_exists('exp', $payload)
        ) {
            // Manually process the payload part to avoid having to drag in a new library
            $expiration = new \DateTime('@' . $payload['exp']);
        } else {
            $expiration = null;
        }

        $this->token = new JwtToken($body[$this->options['token_key']], $expiration);
        $this->tokenPersistence->saveToken($this->token);

        return $this->token;
    }

    /**
     * getHeaders. Return defaults header.
     *
     * @return array
     */
    private function getDefaultHeaders()
    {
        return [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'timeout' => $this->options['timeout'],
            ],
        ];
    }
}
