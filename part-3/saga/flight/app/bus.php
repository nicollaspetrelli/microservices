<?php

declare(strict_types=1);

use Hotel\Domain\Events\DeniedHotelBookingEvent;
use Flight\EventHandlers\DeniedHotelBookingEventHandler;
use Flight\EventHandlers\PendingReservationCreatedEventHandler;
use Reservation\Domain\Events\PendingReservationCreatedEvent;

return [
    PendingReservationCreatedEvent::class => [PendingReservationCreatedEventHandler::class],
    DeniedHotelBookingEvent::class => [DeniedHotelBookingEventHandler::class],
];
