<?php

namespace Eljam\GuzzleJwt\Manager;

use Eljam\GuzzleJwt\JwtToken;
use Eljam\GuzzleJwt\Strategy\Auth\AuthStrategyInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
class JwtManager
{
    const TIMEOUT = 1;

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
     * Constructor.
     *
     * @param ClientInterface       $client
     * @param AuthStrategyInterface $auth
     * @param array                 $options
     */
    public function __construct(
        ClientInterface $client,
        AuthStrategyInterface $auth,
        array $options = []
    ) {
        $this->client = $client;
        $this->auth = $auth;

        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'token_url' => '/token',
        ]);

        $resolver->setRequired(['token_url']);

        $this->options = $resolver->resolve($options);
    }

    /**
     * getToken.
     *
     * @return JwtToken
     */
    public function getJwtToken()
    {
        $url = $this->options['token_url'];

        $requestOptions = array_merge(
            $this->getHeaders(),
            $this->auth->getGuzzleRequestOptions()
        );

        $response = $this->client->request('POST', $url, $requestOptions);
        $body = json_decode($response->getBody(), true);

        return new JwtToken($body['token']);
    }

    /**
     * getHeaders. Return defaults header.
     *
     * @return array
     */
    private function getHeaders()
    {
        return [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'content-type' => 'application/json',
                'timeout' => self::TIMEOUT,
            ],
        ];
    }
}
