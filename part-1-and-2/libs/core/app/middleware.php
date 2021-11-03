<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;
use Slim\Psr7\Response;
use Vcampitelli\Framework\Core\Application\Middleware\AuthenticationMiddleware;
use Vcampitelli\Framework\Core\Application\Middleware\CachingMiddleware;

return function (App $app) {
    define('APP_HOSTNAME', \gethostname());
    $app->add(function (Request $request, RequestHandler $handler) {
        return $handler->handle($request)->withHeader('X-App-Hostname', APP_HOSTNAME);
    });
    if ($_ENV['CACHING'] ?? false) {
        $app->add(CachingMiddleware::class);
    }
    $app->add(AuthenticationMiddleware::class);
};
