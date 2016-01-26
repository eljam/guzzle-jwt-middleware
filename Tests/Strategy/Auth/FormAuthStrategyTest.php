<?php

namespace Eljam\GuzzleJwt\Tests\Strategy\Auth;

use Eljam\GuzzleJwt\Strategy\Auth\FormAuthStrategy;

/**
 * @author Guillaume Cavavana <guillaume.cavana@gmail.com>
 */
class FormAuthStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testGetToken.
     */
    public function testGetToken()
    {
        $authStrategy = new FormAuthStrategy(
            [
                'username' => 'admin',
                'password' => 'admin',
                'form_fields' => ['login', 'password'],
            ]
        );

        $this->assertTrue(array_key_exists('login', $authStrategy->getGuzzleRequestOptions()['form_params']));

        $this->assertTrue(array_key_exists('password', $authStrategy->getGuzzleRequestOptions()['form_params']));
    }
}
