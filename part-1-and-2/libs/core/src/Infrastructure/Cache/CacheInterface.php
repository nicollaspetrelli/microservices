<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Core\Infrastructure\Cache;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface CacheInterface
{
    /**
     * Config that says that should cache a request considering the current user (
     *
     * @var string
     */
    public const CACHE_PER_USER = 'CACHE_PER_USER';

    /**
     * Returns if there is a cache for the specified request
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function has(ServerRequestInterface $request): bool;

    /**
     * Returns a response for the specified request or null if the cache doesn't exist
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface|null
     */
    public function get(ServerRequestInterface $request): ?ResponseInterface;

    /**
     * Inserts a new entry in cache for the specified request
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $stream
     * @return CacheInterface
     */
    public function set(ServerRequestInterface $request, ResponseInterface $stream): CacheInterface;
}
