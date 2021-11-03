<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;
use Framework\Application\Settings\Settings;
use Framework\Application\Settings\SettingsInterface;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            $path = 'php://stdout';
            if (empty($_ENV['docker'])) {
                $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
                $appPath = \dirname($reflection->getFileName(), 3);
                $path = $appPath . '/logs/app.log';
            }

            return new Settings([
                'displayErrorDetails' => ($_ENV['ENVIRONMENT'] ?? '') !== 'production',
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger'              => [
                    'name'  => $_ENV['APP_NAME'] ?? $_ENV['HOSTNAME'],
                    'path'  => $path,
                    'level' => Logger::DEBUG,
                ],
            ]);
        }
    ]);
};
