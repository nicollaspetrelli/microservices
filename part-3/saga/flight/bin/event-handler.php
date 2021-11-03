<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

Framework\Core\EventHandler::run(
    __DIR__,
    [
        "{$_ENV['APP_RABBITMQ_RESERVATION_QUEUE']}_success",
        "{$_ENV['APP_RABBITMQ_HOTEL_QUEUE']}_fail",
    ]
);
