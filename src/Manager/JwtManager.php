<?php

namespace Eljam\GuzzleJwt\Manager;

use Eljam\GuzzleJwt\JwtToken;
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
            'timeout' => 1,
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
        $url = $this->options['token_url'];

        $requestOptions = array_merge(
            $this->getDefautHeaders(),
            $this->auth->getRequestOptions()
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
    private function getDefautHeaders()
    {
        return [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'timeout' => $this->options['timeout'],
            ],
        ];
    }
}
