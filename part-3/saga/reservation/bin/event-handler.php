<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

Framework\Core\EventHandler::run(
    __DIR__,
    [
        "{$_ENV['APP_RABBITMQ_FLIGHT_QUEUE']}_fail",
        "{$_ENV['APP_RABBITMQ_HOTEL_QUEUE']}_success",
    ]
);
