<?php

namespace Eljam\GuzzleJwt\Strategy\Auth;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
class HttpBasicAuthStrategy implements AuthStrategyInterface
{
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
        ]);

        $resolver->setRequired(['username', 'password']);

        $this->options = $resolver->resolve($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getGuzzleRequestOptions()
    {
        return [
            \GuzzleHttp\RequestOptions::AUTH => [
                $this->options['username'],
                $this->options['password'],
            ],
        ];
    }
}
