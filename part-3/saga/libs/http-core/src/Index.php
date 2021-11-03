<?php

declare(strict_types=1);

namespace Framework\Http;

use DI\ContainerBuilder;
use Framework\Core\EventHandler;
use Framework\Http\Application\Handlers\HttpErrorHandler;
use Framework\Http\Application\Handlers\ShutdownHandler;
use Framework\Http\Application\ResponseEmitter\ResponseEmitter;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

/**
 * Class that acts like an index.php file for Slim
 */
class Index
{

    /**
     * Runs the application
     *
     * @param string $dir
     * @param bool   $cache
     *
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public static function run(string $dir, bool $cache = false): void
    {
        // Instantiate PHP-DI ContainerBuilder
        $containerBuilder = new ContainerBuilder();

        if ($cache) {
            $containerBuilder->enableCompilation($dir . '/../var/cache');
        }

        // Set up client dependencies
        $file = $dir . '/../app/dependencies.php';
        if (\is_file($file)) {
            $dependencies = require $file;
            $dependencies($containerBuilder);
        }

        if (\class_exists(EventHandler::class)) {
            EventHandler::loadEventBroker($dir, $containerBuilder);
        }

        // Build PHP-DI Container instance
        $container = $containerBuilder->build();

        // Instantiate the app
        AppFactory::setContainer($container);
        $app = AppFactory::create();
        $callableResolver = $app->getCallableResolver();

        // Register client middleware
        $file = $dir . '/../app/middleware.php';
        if (\is_file($file)) {
            $middleware = require $file;
            $middleware($app);
        }

        // Register client routes
        $file = $dir . '/../app/routes.php';
        if (\is_file($file)) {
            $routes = require $file;
            $routes($app);
        }

        $isProduction = ($_ENV['ENVIRONMENT'] ?? '') === 'production';

        // Create Request object from globals
        $serverRequestCreator = ServerRequestCreatorFactory::create();
        $request = $serverRequestCreator->createServerRequestFromGlobals();

        // Create Error Handler
        $responseFactory = $app->getResponseFactory();
        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

        // Create Shutdown Handler
        $shutdownHandler = new ShutdownHandler($request, $errorHandler, !$isProduction);
        register_shutdown_function($shutdownHandler);

        // Add Routing Middleware
        $app->addRoutingMiddleware();

        // Add Error Middleware
        $errorMiddleware = $app->addErrorMiddleware(!$isProduction, true, !$isProduction);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        // Run App & Emit Response
        $response = $app->handle($request);
        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);
    }

}
