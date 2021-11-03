<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Core\Infrastructure\Cache;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Slim\Psr7\Factory\StreamFactory;
use Redis;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;
use Vcampitelli\Framework\Core\Domain\User\User;

/**
 * Implementation of a Redis-backed cache
 */
class RedisCache implements CacheInterface
{
    /**
     * @var StreamFactory
     */
    private StreamFactory $streamFactory;

    /**
     * @var Redis
     */
    private Redis $redis;

    /**
     * RedisCache constructor.
     *
     * @param string $host
     * @param array $settings = []
     */
    public function __construct(string $host, array $settings = [])
    {
        $this->redis = new Redis();
        $this->redis->connect(
            $host,
            $settings['port'] ?? 6379,
            (float) $settings['timeout'] ?? 0.0,
            $settings['reserved'] ?? null,
            (int) $settings['retryInterval'] ?? 0,
            (float) $settings['readTimeout'] ?? 0.0
        );

        $this->streamFactory = new StreamFactory();
    }

    /**
     * Returns if there is a cache for the specified request
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function has(ServerRequestInterface $request): bool
    {
        return (bool) $this->redis->exists(
            $this->getKey($request)
        );
    }

    /**
     * Returns a response for the specified request or null if the cache doesn't exist
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface|null
     */
    public function get(ServerRequestInterface $request): ?ResponseInterface
    {
        $contents = $this->redis->get(
            $this->getKey($request)
        );
        if (empty($contents)) {
            return null;
        }

        return $this->unserialize($contents);
    }

    /**
     * Inserts a new entry in cache for the specified request
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return CacheInterface
     */
    public function set(ServerRequestInterface $request, ResponseInterface $response): CacheInterface
    {
        $key = $this->getKey($request);

        // @TODO read TTL from some config and probably for each request type
        $this->redis->setEx(
            $key,
            3600,
            $this->serialize($response)
        );

        return $this;
    }

    /**
     * Serializes a request
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getKey(ServerRequestInterface $request): string
    {
        /** @var UriInterface $uri */
        $uri = $request->getUri();

        $key = "{$uri->getHost()}.{$uri->getPort()}:{$uri->getPath()}";

        if ($request->getAttribute(self::CACHE_PER_USER, false)) {
            $key .= ':' . User::getInstance()->getId();
        }

        $query = $request->getQueryParams();
        if (!empty($query)) {
            \ksort($query);
            $key = ':' . \hash('sha512', \json_encode($query));
        }

        return $key;
    }

    /**
     * Serializes the response to be cached
     *
     * @param ResponseInterface $response
     * @return string
     */
    protected function serialize(ResponseInterface $response): string
    {
        $data = [
            'body' => (string) $response->getBody(),
        ];

        $headers = $response->getHeaders();
        if (!empty($headers)) {
            $data['headers'] = $headers;
        }

        $status = $response->getStatusCode();
        if ($status !== StatusCodeInterface::STATUS_OK) {
            $data['status'] = $status;
        }

        return \json_encode($data);
    }

    /**
     * Unserializes the cached response
     *
     * @param string $contents
     * @return ResponseInterface|null
     */
    protected function unserialize(string $contents): ?ResponseInterface
    {
        $data = \json_decode($contents);
        if ((empty($data)) || (!isset($data->body))) {
            return null;
        }

        $headers = (isset($data->headers)) ? (array) $data->headers : [];
        $headers['X-Cache'] = 'HIT';

        return new Response(
            $data->status ?? StatusCodeInterface::STATUS_OK,
            new Headers($headers, []),
            (!empty($data->body)) ? $this->streamFactory->createStream($data->body) : null
        );
    }
}
