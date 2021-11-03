<?php

declare(strict_types=1);

use Amp\Loop;
use Framework\Service\EventStoreDb;
use Users\Domain\User\User;
use Users\Infrastructure\User\UserEventStore;
use Users\Domain\User\Events\UserRegisteredEvent;
use Users\Domain\User\Events\EmailChangedEvent;
use Users\Domain\User\Events\NameChangedEvent;

require __DIR__ . '/../../vendor/autoload.php';

Loop::run(function () {
    $container = Framework\Index::bootstrap(
        __DIR__,
        ($_ENV['ENVIRONMENT'] ?? '') === 'production'
    );

    /** @var EventStoreDb $db */
    $db = $container->get(EventStoreDb::class);
    $db->connect(function () use ($container) {
        /** @var UserEventStore $userEventStore */
        $userEventStore = $container->get(UserEventStore::class);

        $user = new User(
            null,
            'Nicollas Campitelli',
            'nicollas@example.com'
        );

        /** @var \Users\Domain\User\Events\UserEventInterface[] $events */
        $events = [
            new UserRegisteredEvent($user),
            new EmailChangedEvent(
                $user->getId(),
                'nicollas002@example.com'
            ),
            new NameChangedEvent(
                $user->getId(),
                'Nicollas Campitelli Editado'
            ),
            new EmailChangedEvent(
                $user->getId(),
                'nicollas003@example.com'
            ),
        ];

        $promises = [];
        foreach ($events as $event) {
            echo 'Applying event ' . get_class($event) . PHP_EOL;
            $promises[] = $userEventStore->write($event);
        }

        \Amp\Promise\all($promises)->onResolve(function() {
            echo 'Done' . PHP_EOL;
            Loop::stop();
        });
    });
});
