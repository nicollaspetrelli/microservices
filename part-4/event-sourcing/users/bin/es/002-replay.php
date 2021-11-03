<?php

declare(strict_types=1);

use Amp\Loop;
use Framework\Domain\Event\EventInterface;
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
            '70a4bba4-52df-4580-8607-8905e5f8b62d',
            'Vinícius Campitelli',
            'vinicius@example.com'
        );

        $userEventStore->read(
            $user,
            function (EventInterface $event, User $user) {
                echo 'Applying event ' . get_class($event) . ' (' . $event->getEventId() . ')' . PHP_EOL;
                var_dump($user);
                echo PHP_EOL;
            }
        )->onResolve(function () use ($user) {
            echo 'After replay:' . PHP_EOL;
            var_dump($user);
            Loop::stop();
        });
    });
});
