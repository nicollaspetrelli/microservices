<?php

declare(strict_types=1);

namespace Flight\EventHandlers;

use Flight\Domain\Events\DeniedFlightBookingEvent;
use Flight\Domain\Events\FlightBookedEvent;
use Flight\Domain\Flight;
use Flight\Domain\FlightRepositoryInterface;
use Framework\Core\Application\Service\EventBrokerInterface;
use Psr\Log\LoggerInterface;
use Reservation\Domain\Events\PendingReservationCreatedEvent;

final class PendingReservationCreatedEventHandler
{

    /**
     * @param \Framework\Core\Application\Service\EventBrokerInterface $broker
     * @param \Psr\Log\LoggerInterface                                 $logger
     * @param \Flight\Domain\FlightRepositoryInterface                 $flightRepository
     */
    public function __construct(
        private EventBrokerInterface $broker,
        private LoggerInterface $logger,
        private FlightRepositoryInterface $flightRepository
    ) {
    }

    /**
     * @param \Reservation\Domain\Events\PendingReservationCreatedEvent $event
     */
    public function __invoke(PendingReservationCreatedEvent $event): void
    {
        $reservation = $event->getReservation();

        // Simulating a 33% chance of booking denial
        try {
            $isAvailable = \random_int(0, 2) !== 0;
        } catch (\Exception) {
            // Some systems can throw an Exception due to a missing source of randomness
            $isAvailable = true;
        }

        if (!$isAvailable) {
            $reservationId = $reservation->getId();
            $this->logger->warning(
                "Reservation {$reservationId}: There are no flights for these dates anymore"
            );
            $this->broker->publishFailEvent(
                new DeniedFlightBookingEvent(
                    $reservationId,
                    'There are no flights for these dates anymore'
                )
            );

            return;
        }

        $flight = Flight::fromReservation($reservation);
        $flight->setCompletedState();
        $this->flightRepository->save($flight);

        $this->broker->publishSuccessEvent(
            new FlightBookedEvent(
                $flight
            )
        );

        $this->logger->info(
            \sprintf(
                'Booked Flight %s for Reservation %s (%s - %s)',
                $flight->getId(),
                $reservation->getId(),
                $flight->getDateStart()->format(\DateTimeInterface::RFC3339),
                $flight->getDateEnd()->format(\DateTimeInterface::RFC3339)
            )
        );
    }

}
