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
            $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
            $appPath = \dirname($reflection->getFileName(), 3);
            return new Settings([
                'displayErrorDetails' => ($_ENV['ENVIRONMENT'] ?? '') !== 'production',
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger'              => [
                    'name'  => 'slim-app',
                    'path'  => isset($_ENV['docker']) ? 'php://stdout' : $appPath . '/logs/app.log',
                    'level' => Logger::DEBUG,
                ],
            ]);
        }
    ]);
};
