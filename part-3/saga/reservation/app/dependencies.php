<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use MongoDB\Database;
use Psr\Container\ContainerInterface;
use Reservation\Infrastructure\Persistence\MongoReservationRepository;
use Reservation\Domain\ReservationRepositoryInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        ReservationRepositoryInterface::class => function (ContainerInterface $container) {
            return new MongoReservationRepository(
                $container->get(Database::class)->selectCollection('reservations')
            );
        },
    ]);
};
