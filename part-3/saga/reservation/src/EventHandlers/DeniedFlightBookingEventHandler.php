<?php

declare(strict_types=1);

namespace Reservation\EventHandlers;

use Flight\Domain\Events\DeniedFlightBookingEvent;
use Psr\Log\LoggerInterface;
use Reservation\Domain\Reservation;
use Reservation\Domain\ReservationRepositoryInterface;

final class DeniedFlightBookingEventHandler
{
    /**
     * @param LoggerInterface $logger
     * @param ReservationRepositoryInterface $reservationRepository
     */
    public function __construct(
        private LoggerInterface $logger,
        private ReservationRepositoryInterface $reservationRepository
    ) {
    }

    /**
     * @param DeniedFlightBookingEvent $event
     * @throws \Framework\Core\Domain\DomainException\DomainRecordNotFoundException
     */
    public function __invoke(DeniedFlightBookingEvent $event): void
    {
        $reservationId = $event->getReservationId();

        /** @var Reservation $reservation */
        $reservation = $this->reservationRepository->findById($reservationId);

        if ($reservation->isDeniedState()) {
            $this->logger->debug("Reservation {$reservation->getId()} was already denied");

            return;
        }

        if ($reservation->isCompletedState()) {
            $this->logger->debug("Reservation {$reservation->getId()} was already completed");

            return;
        }

        $reason = $event->getReason();
        $reservation->setDeniedState($reason);
        $this->reservationRepository->save($reservation);

        $this->logger->warning(
            "Reservation {$reservation->getId()} was canceled due to a flight booking denial: {$reason}"
        );
    }
}
