<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use MongoDB\Database;
use Flight\Infrastructure\Persistence\MongoFlightRepository;
use Flight\Domain\FlightRepositoryInterface;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        FlightRepositoryInterface::class => function (ContainerInterface $container) {
            return new MongoFlightRepository(
                $container->get(Database::class)->selectCollection('flights')
            );
        },
    ]);
};
