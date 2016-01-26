<?php

namespace Eljam\GuzzleJwt\Strategy\Auth;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
class QueryAuthStrategy extends AbstractBaseAuthStrategy
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'query_fields' => ['username', 'password'],
        ]);

        $resolver->setRequired(['query_fields']);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestOptions()
    {
        return [
            \GuzzleHttp\RequestOptions::QUERY => [
                $this->options['query_fields'][0] => $this->options['username'],
                $this->options['query_fields'][1] => $this->options['password'],
            ],
        ];
    }
}
