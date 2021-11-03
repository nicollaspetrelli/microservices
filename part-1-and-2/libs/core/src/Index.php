<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Core;

use Vcampitelli\Framework\Core\Application\Handlers\HttpErrorHandler;
use Vcampitelli\Framework\Core\Application\Handlers\ShutdownHandler;
use Vcampitelli\Framework\Core\Application\ResponseEmitter\ResponseEmitter;
use Vcampitelli\Framework\Core\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
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

        // Set up framework settings
        $settings = require __DIR__ . '/../app/settings.php';
        $settings($containerBuilder);

        // Set up framework dependencies
        $dependencies = require __DIR__ . '/../app/dependencies.php';
        $dependencies($containerBuilder);
        // Set up client dependencies
        $file = $dir . '/../app/dependencies.php';
        if (\is_file($file)) {
            $dependencies = require $file;
            $dependencies($containerBuilder);
        }

        // Set up framework repositories
        $repositories = require __DIR__ . '/../app/repositories.php';
        $repositories($containerBuilder);
        // Set up client repositories
        $file = $dir . '/../app/repositories.php';
        if (\is_file($file)) {
            $repositories = require $file;
            $repositories($containerBuilder);
        }

        // Build PHP-DI Container instance
        $container = $containerBuilder->build();

        // Set up client settings
        $file = $dir . '/../app/settings.php';
        if (\is_file($file)) {
            $settings = require $file;
            $settings($container->get(SettingsInterface::class));
        }

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
