# Guzzle Jwt middleware

[![Build Status](https://img.shields.io/travis/eljam/guzzle-jwt-middleware.svg?branch=master&style=flat-square)](https://travis-ci.org/eljam/guzzle-jwt-middleware)
[![Code Quality](https://img.shields.io/scrutinizer/g/eljam/guzzle-jwt-middleware.svg?b=master&style=flat-square)](https://scrutinizer-ci.com/g/eljam/guzzle-jwt-middleware/?branch=master)
[![Code Coverage](https://img.shields.io/coveralls/eljam/guzzle-jwt-middleware.svg?style=flat-square)](https://coveralls.io/r/eljam/guzzle-jwt-middleware)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/87bbdd85-2cd8-4556-94c6-5ed9f501cf7d/mini.png)](https://insight.sensiolabs.com/projects/87bbdd85-2cd8-4556-94c6-5ed9f501cf7d)
[![Latest Unstable Version](https://poser.pugx.org/eljam/guzzle-jwt-middleware/v/unstable)](https://packagist.org/packages/eljam/guzzle-jwt-middleware)
[![Latest Stable Version](https://poser.pugx.org/eljam/guzzle-jwt-middleware/v/stable)](https://packagist.org/packages/eljam/guzzle-jwt-middleware)
[![Downloads](https://img.shields.io/packagist/dt/eljam/guzzle-jwt-middleware.svg)](https://packagist.org/packages/eljam/guzzle-jwt-middleware)
[![license](https://img.shields.io/packagist/l/eljam/guzzle-jwt-middleware.svg)](https://github.com/eljam/guzzle-jwt-middleware/blob/master/LICENSE)

## Introduction

Works great with [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle)

## Installation

`composer require eljam/guzzle-jwt-middleware`

## Usage

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

//Optionnal: create your persistence strategy
$persistenceStrategy = null;

$baseUri = 'http://api.example.org/';

// Create authClient
$authClient = new Client(['base_uri' => $baseUri]);

//Create the JwtManager
$jwtManager = new JwtManager(
    $authClient,
    $authStrategy,
    $persistenceStrategy,
    [
        'token_url' => '/api/token',
    ]
);

// Create a HandlerStack
$stack = HandlerStack::create();

// Add middleware
$stack->push(new JwtMiddleware($jwtManager));

$client = new Client(['handler' => $stack, 'base_uri' => $baseUri]);

try {
    $response = $client->get('/api/ping');
    echo($response->getBody());
} catch (TransferException $e) {
    echo $e->getMessage();
}

//response
//{"data":"pong"}

```

## Auth Strategies

### QueryAuthStrategy

```php
$authStrategy = new QueryAuthStrategy(
    [
        'username' => 'admin',
        'password' => 'admin',
        'query_fields' => ['username', 'password'],
    ]
);
```

### FormAuthStrategy

```php
$authStrategy = new FormAuthStrategy(
    [
        'username' => 'admin',
        'password' => 'admin',
        'form_fields' => ['username', 'password'],
    ]
);
```

### HttpBasicAuthStrategy

```php
$authStrategy = new HttpBasicAuthStrategy(
    [
        'username' => 'admin',
        'password' => 'password',
    ]
);
```
### JsonAuthStrategy

```php
$authStrategy = new JsonAuthStrategy(
    [
        'username' => 'admin',
        'password' => 'admin',
        'json_fields' => ['username', 'password'],
    ]
);
```

## Persistence

To avoid requesting a token everytime php runs, you can pass to `JwtManager` an implementation of `TokenPersistenceInterface`.
By default `NullTokenPersistence` will be used.

### Simpe cache adapter (PSR-16)

If you have any [PSR-16 compatible cache](https://www.php-fig.org/psr/psr-16/), you can use it as a persistence handler:

```php
<?php

use Eljam\GuzzleJwt\Persistence\SimpleCacheTokenPersistence;
use Psr\SimpleCache\CacheInterface;

/**
 * @var CacheInterface
 */
$psr16cache;

$persistenceStrategy = new SimpleCacheTokenPersistence($psr16cache);
```

Optionnally you can specify the TTL and cache key used:

```php
<?php

use Eljam\GuzzleJwt\Persistence\SimpleCacheTokenPersistence;
use Psr\SimpleCache\CacheInterface;

/**
 * @var CacheInterface
 */
$psr16cache;

$ttl = 1800;
$cacheKey = 'myUniqueKey';

$persistenceStrategy = new SimpleCacheTokenPersistence($psr16cache, $ttl, $cacheKey);
```


### Custom persistence

You may create you own persistence handler by implementing the `TokenPersistenceInterface`:

```php
namespace App\Jwt\Persistence;

use Eljam\GuzzleJwt\Persistence\TokenPersistenceInterface;

class MyCustomPersistence implements TokenPersistenceInterface
{
    /**
     * Save the token data.
     *
     * @param JwtToken $token
     */
    public function saveToken(JwtToken $token)
    {
        // Use APCu, Redis or whatever fits your needs.
        return;
    }

    /**
     * Retrieve the token from storage and return it.
     * Return null if nothing is stored.
     *
     * @return JwtToken Restored token
     */
    public function restoreToken()
    {
        return null;
    }

    /**
     * Delete the saved token data.
     */
    public function deleteToken()
    {
        return;
    }

    /**
     * Returns true if a token exists (although it may not be valid)
     *
     * @return bool
     */
    public function hasToken()
    {
        return false;
    }
}
```

## Token key


### Property accessor

With the property accessor you can point to a node in your json.

Json Example:

```javascript
{
    "status": "success",
    "message": "Login successful",
    "payload": {
        "token": "1453720507"
    },
    "expires_in": 3600
}
```

Library configuration:

```php
$jwtManager = new JwtManager(
    $authClient,
    $authStrategy,
    $persistenceStrategy,
    [
        'token_url'  => '/api/token',
        'token_key'  => 'payload.token',
        'expire_key' => 'expires_in'
    ]
);
```

## Default behavior
By default this library assumes your json response has a key `token`, something like this:

```javascript
{
    token: "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9..."
}
```

but now you can change the token_key in the JwtManager options:

```php
$jwtManager = new JwtManager(
    $authClient,
    $authStrategy,
    $persistenceStrategy,
    [
        'token_url' => '/api/token',
        'token_key' => 'access_token',
    ]
);
```

### Null property accessor

If the response is not in json format (i.e. the response body contains only the token in plain text format), you need 
to set the property accessor to `null`:

```php
//Create the JwtManager
$jwtManager = new JwtManager(
    $authClient,
    $authStrategy,
    $persistenceStrategy,
    [
        'token_url' => '/api/token',
    ]
);
//Set property accessor to `null`
$jwtManager->setPropertyAccessor(null);
```


## Authorization Header Type

Some endpoints use different Authorization header types (Bearer, JWT, etc...).

The default is Bearer, but another type can be supplied in the middleware:

```php
$stack->push(new JwtMiddleware($jwtManager, 'JWT'));
```

## Cached token

To avoid too many calls between multiple request, there is a cache system.

Json example:

```javascript
{
    token: "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9...",
    expires_in: "3600"
}
```

```php
$jwtManager = new JwtManager(
    $authClient,
    $authStrategy,
    $persistenceStrategy,
    [
        'token_url' => '/api/token',
        'token_key' => 'access_token',
        'expire_key' => 'expires_in', # default is expires_in if not set
    ]
);
```

The bundle natively supports the [exp field](https://tools.ietf.org/html/rfc7519.html#section-4.1.4) in the JWT payload.
