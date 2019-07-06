<?php

namespace Eljam\GuzzleJwt\Persistence;

use Eljam\GuzzleJwt\JwtToken;
use Eljam\GuzzleJwt\Persistence\TokenPersistenceInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * PersistenceInterface backed by a PSR-16 SimpleCache
 * @author Nicolas Reynis (nreynis)
 */
class SimpleCacheTokenPersistence implements TokenPersistenceInterface
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @var string
     */
    private $cacheKey;

    public function __construct(CacheInterface $cache, $ttl = 1800, $cacheKey = 'eljam.jwt.token')
    {
        $this->cache = $cache;
        $this->ttl = $ttl;
        $this->cacheKey = $cacheKey;
    }

    /**
     * @inheritDoc
     */
    public function saveToken(JwtToken $token)
    {
        /*
         * TTL does not need to match token expiration,
         * it'll be revalidated by manager so we can safely
         * return a stale token.
         */
        $this->cache->set($this->cacheKey, $token, $this->ttl);
        return;
    }

    /**
     * @inheritDoc
     */
    public function restoreToken()
    {
        return $this->cache->get($this->cacheKey);
    }

    /**
     * @inheritDoc
     */
    public function deleteToken()
    {
        $this->cache->delete($this->cacheKey);
        return;
    }

    /**
     * @inheritDoc
     */
    public function hasToken()
    {
        return $this->cache->has($this->cacheKey);
    }
}
