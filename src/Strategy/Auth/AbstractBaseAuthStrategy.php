<?php

namespace Eljam\GuzzleJwt\Strategy\Auth;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
abstract class AbstractBaseAuthStrategy implements AuthStrategyInterface
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
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    /**
     * configureOptions.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'username' => '',
            'password' => '',
        ));

        $resolver->setRequired(['username', 'password']);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getRequestOptions();
}
