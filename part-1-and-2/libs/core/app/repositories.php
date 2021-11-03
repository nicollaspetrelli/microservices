<?php
declare(strict_types=1);

use Vcampitelli\Framework\Core\Infrastructure\Persistence\User\UserRepository;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
            UserRepository::class => \Di\autowire(UserRepository::class),
        ]
    );
};
