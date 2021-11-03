<?php

declare(strict_types=1);

namespace Hotel\EventHandlers;

use Flight\Domain\Events\FlightBookedEvent;
use Framework\Core\Application\Service\EventBrokerInterface;
use Hotel\Domain\Events\DeniedHotelBookingEvent;
use Hotel\Domain\Events\HotelBookedEvent;
use Hotel\Domain\Hotel;
use Hotel\Domain\HotelRepositoryInterface;
use Psr\Log\LoggerInterface;

final class FlightBookedEventHandler
{

    /**
     * @param \Framework\Core\Application\Service\EventBrokerInterface $broker
     * @param \Psr\Log\LoggerInterface                                 $logger
     * @param \Hotel\Domain\HotelRepositoryInterface                   $hotelRepository
     */
    public function __construct(
        private EventBrokerInterface $broker,
        private LoggerInterface $logger,
        private HotelRepositoryInterface $hotelRepository
    ) {
    }

    /**
     * @param \Flight\Domain\Events\FlightBookedEvent $event
     */
    public function __invoke(FlightBookedEvent $event): void
    {
        $flight = $event->getFlight();
        if (!$flight->getId()) {
            $this->logger->warning('Invalid flight provided');
            $this->broker->publishFailEvent(
                new DeniedHotelBookingEvent(
                    $flight,
                    'Invalid flight provided'
                )
            );

            return;
        }
        if (!$flight->isCompletedState()) {
            $this->logger->warning(
                "Flight {$flight->getId()}: Invalid flight state provided"
            );
            $this->broker->publishFailEvent(
                new DeniedHotelBookingEvent(
                    $flight,
                    'Invalid flight state provided'
                )
            );

            return;
        }

        $reservationId = $flight->getReservationId();

        // Simulating a 33% chance of booking denial
        try {
            $isAvailable = \random_int(0, 2) !== 0;
        } catch (\Exception) {
            // Some systems can throw an Exception due to a missing source of randomness
            $isAvailable = true;
        }

        if (!$isAvailable) {
            $this->logger->warning(
                "Reservation {$reservationId}: There is no room left in this hotel"
            );
            $this->broker->publishFailEvent(
                new DeniedHotelBookingEvent(
                    $flight,
                    'There is no room left in this hotel'
                )
            );

            return;
        }

        $hotel = Hotel::fromFlight($flight);
        $hotel->setCompletedState();
        $this->hotelRepository->save($hotel);

        $this->broker->publishSuccessEvent(
            new HotelBookedEvent(
                $hotel
            )
        );

        $this->logger->info(
            \sprintf(
                'Booked Hotel %s for Flight %s / Reservation %s (%s - %s)',
                $hotel->getId(),
                $flight->getId(),
                $reservationId,
                $hotel->getDateStart()->format(\DateTimeInterface::RFC3339),
                $hotel->getDateEnd()->format(\DateTimeInterface::RFC3339)
            )
        );
    }

}
