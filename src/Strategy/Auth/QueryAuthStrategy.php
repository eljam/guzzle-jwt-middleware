<?php

namespace Eljam\GuzzleJwt\Strategy\Auth;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
class QueryAuthStrategy implements AuthStrategyInterface
{
    /**
     * $options.
     *
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'username' => '',
            'password' => '',
            'query_fields' => ['username', 'password'],
        ]);

        $resolver->setRequired(['query_fields', 'username', 'password']);

        $this->options = $resolver->resolve($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getGuzzleRequestOptions()
    {
        return [
            \GuzzleHttp\RequestOptions::QUERY => [
                $this->options['query_fields'][0] => $this->options['username'],
                $this->options['query_fields'][1] => $this->options['password'],
            ],
        ];
    }
}
