<?php

declare(strict_types=1);

namespace Flight\EventHandlers;

use Flight\Domain\Events\DeniedFlightBookingEvent;
use Flight\Domain\FlightRepositoryInterface;
use Framework\Core\Application\Service\EventBrokerInterface;
use Hotel\Domain\Events\DeniedHotelBookingEvent;
use Psr\Log\LoggerInterface;

final class DeniedHotelBookingEventHandler
{

    /**
     * @param \Framework\Core\Application\Service\EventBrokerInterface $eventBroker
     * @param \Psr\Log\LoggerInterface                                 $logger
     * @param \Flight\Domain\FlightRepositoryInterface                 $flightRepository
     */
    public function __construct(
        private EventBrokerInterface $eventBroker,
        private LoggerInterface $logger,
        private FlightRepositoryInterface $flightRepository
    ) {
    }

    /**
     * @param \Hotel\Domain\Events\DeniedHotelBookingEvent $event
     */
    public function __invoke(DeniedHotelBookingEvent $event): void
    {
        $flight = $event->getFlight();
        if (!$flight->getId()) {
            throw new \InvalidArgumentException('Invalid flight provided');
        }
        if ($flight->isDeniedState()) {
            $this->logger->debug("Flight {$flight->getId()} is already canceled");
            return;
        }
        if ($flight->isPendingState()) {
            $this->logger->debug("Flight {$flight->getId()} is not confirmed");
            return;
        }

        $reservationId = $flight->getReservationId();

        $reason = $event->getReason();
        $flight->setDeniedState("Hotel booking was denied: {$reason}");
        $this->flightRepository->save($flight);

        $this->eventBroker->publishFailEvent(
            new DeniedFlightBookingEvent(
                $reservationId,
                "Hotel booking was denied: {$reason}"
            )
        );

        $this->logger->notice(
            "Reservation {$reservationId}: Flight {$flight->getId()} was denied due to hotel ".
            "booking denial: {$reason}"
        );
    }

}
