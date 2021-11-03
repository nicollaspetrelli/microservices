<?php

declare(strict_types=1);

use Flight\Domain\Events\DeniedFlightBookingEvent;
use Hotel\Domain\Events\HotelBookedEvent;
use Reservation\EventHandlers\DeniedFlightBookingEventHandler;
use Reservation\EventHandlers\HotelBookedEventHandler;

return [
    DeniedFlightBookingEvent::class => [DeniedFlightBookingEventHandler::class],
    HotelBookedEvent::class => HotelBookedEventHandler::class,
];
