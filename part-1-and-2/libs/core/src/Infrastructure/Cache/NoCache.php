<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Core\Infrastructure\Cache;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Redis;

/**
 * Bogus implementation for a system with no caching available
 */
class NoCache implements CacheInterface
{
    /**
     * Returns if there is a cache for the specified request
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function has(ServerRequestInterface $request): bool
    {
        return true;
    }

    /**
     * Returns a response for the specified request or null if the cache doesn't exist
     *
     * @param RequestInterface $request
     * @return ResponseInterface|null
     */
    public function get(ServerRequestInterface $request): ?ResponseInterface
    {
        return null;
    }

    /**
     * Inserts a new entry in cache for the specified request
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $stream
     * @return CacheInterface
     */
    public function set(ServerRequestInterface $request, ResponseInterface $stream): CacheInterface
    {
        return $this;
    }
}
