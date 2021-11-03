<?php

declare(strict_types=1);

namespace Framework;

use DI\ContainerBuilder;
use Framework\Application\Settings\SettingsInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractBootstrapper
{

    /**
     * @param  string  $dir
     * @param  bool    $cache
     *
     * @return \Psr\Container\ContainerInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public static function bootstrap(string $dir, bool $cache = false): ContainerInterface
    {
        // Instantiate PHP-DI ContainerBuilder
        $containerBuilder = new ContainerBuilder();

        if ($cache) {
            $containerBuilder->enableCompilation($dir . '/../var/cache');
        }

        // Set up framework settings
        $settings = require __DIR__ . '/../app/settings.php';
        $settings($containerBuilder);

        // Set up framework bus handlers
        $busDefinitions = require __DIR__ . '/../app/bus.php';
        // Set up client bus handlers
        $file = $dir . '/../app/bus.php';
        if (\is_file($file)) {
            $definitions = require $file;
            if (\is_array($definitions)) {
                $busDefinitions += $definitions;
            }
        }

        // Set up framework dependencies
        $dependencies = require __DIR__ . '/../app/dependencies.php';
        $dependencies($containerBuilder, $busDefinitions);
        // Set up client dependencies
        $file = $dir . '/../app/dependencies.php';
        if (\is_file($file)) {
            $dependencies = require $file;
            $dependencies($containerBuilder);
        }

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

        return $container;
    }

    /**
     * Runs the application
     *
     * @param  string  $dir
     * @param  bool    $cache
     */
    abstract public static function run(string $dir, bool $cache = false): void;

}
