<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Framework\Core\Application\Service\EventBrokerInterface;
use Framework\Core\Application\Service\RabbitMqBroker;
use MongoDB\Client;
use MongoDB\Database;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

return function (ContainerBuilder $containerBuilder, array $definitions) {
    $containerBuilder->addDefinitions([
        EventBrokerInterface::class => function () {
            return new RabbitMqBroker(
                "{$_ENV['APP_NAME']}_success",
                "{$_ENV['APP_NAME']}_fail",
                [
                    'host' => $_ENV['APP_RABBITMQ_HOST'] ?? 'rabbitmq',
                    'port' => $_ENV['APP_RABBITMQ_PORT'] ?? 5672,
                    'user' => $_ENV['APP_RABBITMQ_USER'] ?? 'guest',
                    'pass' => $_ENV['APP_RABBITMQ_PASS'] ?? 'guest',
                    'exchange' => $_ENV['APP_RABBITMQ_EXCHANGE'] ?? 'router',
                ]
            );
        },
        MessageBus::class           => function (ContainerInterface $container) use ($definitions) {
            foreach ($definitions as $class => $handlers) {
                if (!\is_array($handlers)) {
                    $handlers = [$handlers];
                }
                foreach ($handlers as $key => $handler) {
                    if (\is_string($handler)) {
                        $handlers[$key] = function ($message) use ($container, $handler) {
                            $instance = $container->get($handler);

                            return $instance($message);
                        };
                    }
                }
                $definitions[$class] = $handlers;
            }

            return new MessageBus(
                [
                    new HandleMessageMiddleware(
                        new HandlersLocator($definitions)
                    ),
                ]
            );
        },
        LoggerInterface::class => function (ContainerInterface $c) {
            $path = 'php://stdout';
            if (!isset($_ENV['docker'])) {
                $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
                $appPath = \dirname($reflection->getFileName(), 3);
                $path = $appPath . '/logs/app.log';
            }

            $logger = new Logger($_ENV['APP_NAME'] ?? $_ENV['HOSTNAME']);

            $handler = new StreamHandler(
                $path,
                Logger::DEBUG
            );
            $logger->pushHandler($handler);

            return $logger;
        },
        Database::class             => function () {
            $options = [];
            if (!empty($_ENV['APP_MONGO_USERNAME'])) {
                $options['username'] = $_ENV['APP_MONGO_USERNAME'];
            }
            if (!empty($_ENV['APP_MONGO_PASSWORD'])) {
                $options['password'] = $_ENV['APP_MONGO_PASSWORD'];
            }

            return (new Client($_ENV['APP_MONGO_DSN'] ?? 'mongodb://localhost:27017', $options))
                ->selectDatabase($_ENV['APP_MONGO_DATABASE'] ?? 'db');
        },
    ]);
};
