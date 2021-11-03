<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Framework\Application\Service\CommandBrokerInterface;
use Framework\Application\Service\EventBrokerInterface;
use Framework\Application\Service\RabbitMqBroker;
use Framework\Application\Settings\SettingsInterface;
use MongoDB\Client as Mongo;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

use function Framework\autoWireHandler;

return function (ContainerBuilder $containerBuilder, array $definitions) {
    $rabbitMqBroker = function () {
        return new RabbitMqBroker(
            $_ENV['APP_RABBITMQ_COMMAND_QUEUE'],
            $_ENV['APP_RABBITMQ_EVENT_QUEUE'],
            [
                'host'     => $_ENV['APP_RABBITMQ_HOST'] ?? 'rabbitmq',
                'port'     => $_ENV['APP_RABBITMQ_PORT'] ?? 5672,
                'user'     => $_ENV['APP_RABBITMQ_USER'] ?? 'guest',
                'pass'     => $_ENV['APP_RABBITMQ_PASS'] ?? 'guest',
                'exchange' => $_ENV['APP_RABBITMQ_EXCHANGE'] ?? 'router',
            ]
        );
    };

    $containerBuilder->addDefinitions([
        LoggerInterface::class        => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        CommandBrokerInterface::class => $rabbitMqBroker,
        EventBrokerInterface::class   => $rabbitMqBroker,
        MessageBus::class             => function (ContainerInterface $container) use ($definitions) {
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
        Mongo::class                  => function () {
            $options = [];
            if (!empty($_ENV['APP_MONGO_USERNAME'])) {
                $options['username'] = $_ENV['APP_MONGO_USERNAME'];
            }
            if (!empty($_ENV['APP_MONGO_PASSWORD'])) {
                $options['password'] = $_ENV['APP_MONGO_PASSWORD'];
            }
            return (new Mongo($_ENV['APP_MONGO_DSN'] ?? 'mongodb://localhost:27017', $options))
                ->selectDatabase($_ENV['APP_MONGO_DATABASE'] ?? 'db');
        },
        PDO::class                    => function () {
            return new PDO(
                $_ENV['APP_PDO_DSN'],
                null,
                null,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
        },
    ]);
};
