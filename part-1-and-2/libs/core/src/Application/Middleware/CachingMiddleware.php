<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Core\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Vcampitelli\Framework\Core\Infrastructure\Cache\CacheInterface;

/**
 * Middleware that returns a caching response for GET requests
 */
class CachingMiddleware implements Middleware
{
    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * CachingMiddleware constructor.
     * @param CacheInterface|null $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     *
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $isGetRequest = $request->getMethod() === 'GET';

        if (($isGetRequest) && ($this->cache->has($request))) {
            $response = $this->cache->get($request);
            if ($response !== null) {
                return $response;
            }
        }

        $response = $handler->handle($request);

        if ($isGetRequest) {
            $this->cache->set($request, $response);
        }

        return $response;
    }
}
