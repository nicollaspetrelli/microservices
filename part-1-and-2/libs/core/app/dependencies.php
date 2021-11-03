<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Vcampitelli\Framework\Acl;
use Vcampitelli\Framework\Core\Application\Middleware;
use Vcampitelli\Framework\Core\Application\Settings\SettingsInterface;
use Vcampitelli\Framework\Core\Domain\User\User;
use Vcampitelli\Framework\Core\Infrastructure\Cache;
use Vcampitelli\Framework\Notification\Service\NotificationServiceInterface;
use Vcampitelli\Framework\Notification\Service\RabbitMqService;


return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        Cache\CacheInterface::class => function (ContainerInterface $c) {
            if (\class_exists(\Redis::class)) {
                $settings = $c->get(SettingsInterface::class)->get('redis', []);
                return new Cache\RedisCache(
                    $settings['host'] ?? 'localhost',
                    $settings
                );
            }

            return new Cache\NoCache();
        },
        Middleware\AuthenticationMiddleware::class => \Di\autowire(Middleware\AuthenticationMiddleware::class),
        Middleware\CachingMiddleware::class => \Di\autowire(Middleware\CachingMiddleware::class),
        Acl\AclClientInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class)->get('acl', []);
            return new Acl\AclHttpClient(
                $settings['url']
            );
        },
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        User::class => function (ContainerInterface $c) {
            return User::getInstance();
        },
    ]);

    $containerBuilder->addDefinitions([
        NotificationServiceInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class)->get('rabbitmq', []);
            return new RabbitMqService($settings['queue'], $settings);
        },
    ]);
};
