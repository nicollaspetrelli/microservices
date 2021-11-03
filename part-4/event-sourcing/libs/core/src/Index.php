<?php

declare(strict_types=1);

namespace Framework;

use Framework\Application\Handlers\HttpErrorHandler;
use Framework\Application\Handlers\ShutdownHandler;
use Framework\Application\ResponseEmitter\ResponseEmitter;
use Framework\Application\Settings\SettingsInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

/**
 * Class that acts like an index.php file for Slim
 */
class Index extends AbstractBootstrapper
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
        $container = self::bootstrap($dir, $cache);

        // Instantiate the app
        AppFactory::setContainer($container);
        $app = AppFactory::create();
        $callableResolver = $app->getCallableResolver();

        // Register framework middleware
        $middleware = require __DIR__ . '/../app/middleware.php';
        $middleware($app);

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

        /** @var SettingsInterface $settings */
        $settings = $container->get(SettingsInterface::class);

        $displayErrorDetails = $settings->get('displayErrorDetails');
        $logError = $settings->get('logError');
        $logErrorDetails = $settings->get('logErrorDetails');

        // Create Request object from globals
        $serverRequestCreator = ServerRequestCreatorFactory::create();
        $request = $serverRequestCreator->createServerRequestFromGlobals();

        // Create Error Handler
        $responseFactory = $app->getResponseFactory();
        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

        // Create Shutdown Handler
        $shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
        register_shutdown_function($shutdownHandler);

        // Add Routing Middleware
        $app->addRoutingMiddleware();

        // Add Error Middleware
        $errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        // Run App & Emit Response
        $response = $app->handle($request);
        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);
    }

}
