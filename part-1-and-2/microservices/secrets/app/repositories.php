<?php

declare(strict_types=1);

use Secrets\Domain\Secret\SecretDataRepository;
use Secrets\Domain\Secret\SecretRepository;
use Secrets\Infrastructure\Persistence\Secret\PdoSecretDataRepository;
use Secrets\Infrastructure\Persistence\Secret\PdoSecretRepository;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Vcampitelli\Framework\Core\Application\Settings\SettingsInterface;

use function Vcampitelli\Framework\Core\autowireRepository;
use function Vcampitelli\Framework\Core\wireRepository;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our SecretRepository interface to its in memory implementation
    $containerBuilder->addDefinitions(
        [
            SecretDataRepository::class => autowireRepository(PdoSecretDataRepository::class),
            SecretRepository::class => function (ContainerInterface $c) {
                $settings = $c->get(SettingsInterface::class);
                $repository = new PdoSecretRepository(
                    $c->get(SecretDataRepository::class),
                    $settings->get('key')
                );

                return wireRepository($c, $repository);
            },
        ]
    );
};
