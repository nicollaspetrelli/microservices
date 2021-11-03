<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use MongoDB\Database;
use Hotel\Infrastructure\Persistence\MongoHotelRepository;
use Hotel\Domain\HotelRepositoryInterface;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        HotelRepositoryInterface::class => function (ContainerInterface $container) {
            return new MongoHotelRepository(
                $container->get(Database::class)->selectCollection('hotels')
            );
        },
    ]);
};
