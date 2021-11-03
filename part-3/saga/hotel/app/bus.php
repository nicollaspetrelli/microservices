<?php

declare(strict_types=1);

use Flight\Domain\Events\FlightBookedEvent;
use Hotel\EventHandlers\FlightBookedEventHandler;

return [
    FlightBookedEvent::class => [FlightBookedEventHandler::class],
];
