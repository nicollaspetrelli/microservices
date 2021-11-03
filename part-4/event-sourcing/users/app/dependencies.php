<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use MongoDB\Client as Mongo;
use Psr\Container\ContainerInterface;
use Users\Application\EventHandlers\UserRegisteredEventHandler;
use Users\Domain\User\UserQueryInterface;
use Users\Domain\User\UserStoreInterface;
use Users\Infrastructure\User\UserMongoQuery;
use Users\Infrastructure\User\UserEventStore;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
            UserQueryInterface::class => function (ContainerInterface $container) {
                return new UserMongoQuery(
                    $container->get(Mongo::class)->selectCollection('users')
                );
            },
            UserStoreInterface::class => function (ContainerInterface $container) {
                return new UserEventStore(
                    $container->get(\PDO::class),
                    'users',
                    'id'
                );
            },
            UserRegisteredEventHandler::class => function (ContainerInterface $container) {
                return new UserRegisteredEventHandler(
                    $container->get(Mongo::class)->selectCollection('users')
                );
            },
        ]
    );
};
