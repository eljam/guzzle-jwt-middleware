<?php

namespace Eljam\GuzzleJwt\Strategy\Auth;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
class FormAuthStrategy implements AuthStrategyInterface
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
            'form_fields' => ['_username', '_password'],
        ]);

        $resolver->setRequired(['form_fields', 'username', 'password']);

        $this->options = $resolver->resolve($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getGuzzleRequestOptions()
    {
        return [
            \GuzzleHttp\RequestOptions::FORM_PARAMS => [
                $this->options['form_fields'][0] => $this->options['username'],
                $this->options['form_fields'][1] => $this->options['password'],
            ],
        ];
    }
}
