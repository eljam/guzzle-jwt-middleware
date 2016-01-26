<?php

namespace Eljam\GuzzleJwt\Strategy\Auth;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
class FormAuthStrategy extends AbstractBaseAuthStrategy
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'form_fields' => ['_username', '_password'],
        ]);

        $resolver->setRequired(['form_fields']);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestOptions()
    {
        return [
            \GuzzleHttp\RequestOptions::FORM_PARAMS => [
                $this->options['form_fields'][0] => $this->options['username'],
                $this->options['form_fields'][1] => $this->options['password'],
            ],
        ];
    }
}
