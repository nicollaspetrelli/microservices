<?php

declare(strict_types=1);

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Vcampitelli\Framework\Core\Application\Settings\Settings;
use Vcampitelli\Framework\Core\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            $settings = [
                'displayErrorDetails' => ($_ENV['ENVIRONMENT'] ?? '') !== 'production',
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger'              => [
                    'name'  => 'slim-app',
                    'path'  => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
            ];

            if (\class_exists(Pdo::class)) {
                $settings['pdo'] = [
                    'dsn' => $_ENV['APP_PDO_DSN'] ?? '',
                ];
            }

            if (\class_exists(Redis::class)) {
                $settings['redis'] = [
                    'host' => $_ENV['APP_REDIS_HOST'] ?? 'localhost',
                    'port' => $_ENV['APP_REDIS_PORT'] ?? 6379,
                    'timeout' => $_ENV['APP_REDIS_TIMEOUT'] ?? 0.0,
                    'reserved' => $_ENV['APP_REDIS_RESERVED'] ?? null,
                    'retryInterval' => $_ENV['APP_REDIS_RETRY_INTERVAL'] ?? 0,
                    'readTimeout' => $_ENV['APP_REDIS_READ_TIMEOUT'] ?? 0.0
                ];
            }

            if (\class_exists(AMQPStreamConnection::class)) {
                $settings['rabbitmq'] = [
                    'host' => $_ENV['APP_RABBITMQ_HOST'] ?? 'rabbitmq',
                    'port' => $_ENV['APP_RABBITMQ_PORT'] ?? 5672,
                    'user' => $_ENV['APP_RABBITMQ_USER'] ?? 'guest',
                    'pass' => $_ENV['APP_RABBITMQ_PASS'] ?? 'guest',
                    'exchange' => $_ENV['APP_RABBITMQ_EXCHANGE'] ?? 'router',
                    'queue' => $_ENV['APP_RABBITMQ_NOTIFICATIONS_QUEUE'],
                ];
            }

            if (isset($_ENV['APP_ACL_URL'])) {
                $settings['acl'] = [
                    'url' => $_ENV['APP_ACL_URL'],
                ];
            }

            return new Settings($settings);
        }
    ]);
};
