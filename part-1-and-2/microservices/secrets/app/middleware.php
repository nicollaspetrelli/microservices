<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Vcampitelli\Framework\Core\Infrastructure\Cache\CacheInterface;

return function (App $app) {
    // This means we want to cache the response on a user basis
    $app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        return $handler->handle(
            $request->withAttribute(CacheInterface::CACHE_PER_USER, true)
        );
    });
};
