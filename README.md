# Guzzle Jwt middleware

[![Build Status](https://img.shields.io/travis/eljam/guzzle-jwt-middleware.svg?branch=master&style=flat-square)](https://travis-ci.org/eljam/guzzle-jwt-middleware) [![Code Quality](https://img.shields.io/scrutinizer/g/eljam/guzzle-jwt-middleware.svg?b=master&style=flat-square)](https://scrutinizer-ci.com/g/eljam/guzzle-jwt-middleware/?branch=master) [![Code Coverage](https://img.shields.io/coveralls/eljam/guzzle-jwt-middleware.svg?style=flat-square)](https://coveralls.io/r/eljam/guzzle-jwt-middleware) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/87bbdd85-2cd8-4556-94c6-5ed9f501cf7d/mini.png)](https://insight.sensiolabs.com/projects/87bbdd85-2cd8-4556-94c6-5ed9f501cf7d) [![Latest Unstable Version](https://poser.pugx.org/eljam/guzzle-jwt-middleware/v/unstable)](https://packagist.org/packages/eljam/guzzle-jwt-middleware)
[![Latest Stable Version](https://poser.pugx.org/eljam/guzzle-jwt-middleware/v/stable)](https://packagist.org/packages/eljam/guzzle-jwt-middleware)

## Installation

`composer require eljam/guzzle-jwt-middleware`

## How to

```php
<?php

use Eljam\GuzzleJwt\JwtMiddleware;
use Eljam\GuzzleJwt\Manager\JwtManager;
use Eljam\GuzzleJwt\Strategy\Auth\QueryAuthStrategy;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

require_once 'vendor/autoload.php';

//Create your auth strategy
$authStrategy = new QueryAuthStrategy(['username' => 'admin', 'password' => 'admin']);

$baseUri = 'http://api.example.org/';

// Create authClient
$authClient = new Client(['base_uri' => $baseUri]);

//Create the JwtManager
$jwtManager = new JwtManager(
    $authClient,
    $authStrategy,
    [
        'token_url' => '/api/token',
    ]
);

// Create a HandlerStack
$stack = HandlerStack::create();

// Add middleware
$stack->push(new JwtMiddleware($jwtManager));

$client = new Client(['handler' => $stack, 'base_uri' => $baseUri]);

$response = $client->get('/api/ping');

try {
    $response = $client->get('/api/ping');
    echo($response->getBody());
} catch (TransferException $e) {
    echo $e->getMessage();
}

//response
//{"data":"pong"}

```
 