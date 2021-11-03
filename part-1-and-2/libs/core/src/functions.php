<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Core;

use Psr\Container\ContainerInterface;
use Vcampitelli\Framework\Core\Application\Settings\SettingsInterface;
use Vcampitelli\Framework\Core\Infrastructure\Persistence\AbstractPdoRepository;

if (!function_exists(__NAMESPACE__ . '\wireRepository')) {
    function wireRepository(ContainerInterface $container, AbstractPdoRepository $repository)
    {
        $repository->setPdo(
            $container->get(SettingsInterface::class)->get('pdo', [])
        );

        return $repository;
    }
}

if (!function_exists(__NAMESPACE__ . '\autowireRepository')) {
    function autowireRepository(string $className)
    {
        return function(ContainerInterface $container) use ($className) {
            return wireRepository($container, new $className());
        };
    }
}
