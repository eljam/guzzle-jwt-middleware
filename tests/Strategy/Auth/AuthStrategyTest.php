<?php

namespace Eljam\GuzzleJwt\Tests\Strategy\Auth;

use Eljam\GuzzleJwt\Strategy\Auth\FormAuthStrategy;
use Eljam\GuzzleJwt\Strategy\Auth\HttpBasicAuthStrategy;
use Eljam\GuzzleJwt\Strategy\Auth\JsonAuthStrategy;
use Eljam\GuzzleJwt\Strategy\Auth\QueryAuthStrategy;

/**
 * @author Guillaume Cavavana <guillaume.cavana@gmail.com>
 */
class AuthStrategyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * testFormAuthStrategy.
     */
    public function testFormAuthStrategy()
    {
        $authStrategy = new FormAuthStrategy(
            [
                'username' => 'admin',
                'password' => 'admin',
                'form_fields' => ['login', 'password'],
            ]
        );

        $this->assertTrue(array_key_exists('login', $authStrategy->getRequestOptions()['form_params']));

        $this->assertTrue(array_key_exists('password', $authStrategy->getRequestOptions()['form_params']));

        $this->assertEquals('admin', $authStrategy->getRequestOptions()['form_params']['login']);
        $this->assertEquals('admin', $authStrategy->getRequestOptions()['form_params']['password']);
    }

    /**
     * tesQueryAuthStrategy.
     */
    public function testQueryAuthStrategy()
    {
        $authStrategy = new QueryAuthStrategy(
            [
                'username' => 'admin',
                'password' => 'admin',
                'query_fields' => ['username', 'password'],
            ]
        );

        $this->assertTrue(array_key_exists('username', $authStrategy->getRequestOptions()['query']));
        $this->assertTrue(array_key_exists('password', $authStrategy->getRequestOptions()['query']));

        $this->assertEquals('admin', $authStrategy->getRequestOptions()['query']['username']);
        $this->assertEquals('admin', $authStrategy->getRequestOptions()['query']['password']);
    }

    /**
     * testHttpBasicAuthStrategy.
     */
    public function testHttpBasicAuthStrategy()
    {
        $authStrategy = new HttpBasicAuthStrategy(
            [
                'username' => 'admin',
                'password' => 'password',
            ]
        );

        $this->assertEquals('admin', $authStrategy->getRequestOptions()['auth'][0]);
        $this->assertEquals('password', $authStrategy->getRequestOptions()['auth'][1]);
    }

    /**
     * testJsonAuthStrategy.
     */
    public function testJsonAuthStrategy()
    {
        $authStrategy = new JsonAuthStrategy(
            [
                'username' => 'admin',
                'password' => 'admin',
                'json_fields' => ['login', 'password'],
            ]
        );

        $this->assertTrue(array_key_exists('login', $authStrategy->getRequestOptions()['json']));

        $this->assertTrue(array_key_exists('password', $authStrategy->getRequestOptions()['json']));

        $this->assertEquals('admin', $authStrategy->getRequestOptions()['json']['login']);
        $this->assertEquals('admin', $authStrategy->getRequestOptions()['json']['password']);
    }
}
