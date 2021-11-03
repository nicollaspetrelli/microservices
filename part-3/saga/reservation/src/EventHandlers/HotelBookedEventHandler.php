<?php

declare(strict_types=1);

namespace Reservation\EventHandlers;

use Hotel\Domain\Events\HotelBookedEvent;
use Psr\Log\LoggerInterface;
use Reservation\Domain\ReservationRepositoryInterface;

final class HotelBookedEventHandler
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
     * @param HotelBookedEvent $event
     * @throws \Framework\Core\Domain\DomainException\DomainRecordNotFoundException
     */
    public function __invoke(HotelBookedEvent $event): void
    {
        $hotel = $event->getHotel();
        if (!$hotel->getId()) {
            throw new \InvalidArgumentException('Invalid hotel provided');
        }
        if (!$hotel->isCompletedState()) {
            throw new \InvalidArgumentException('Invalid hotel state provided');
        }

        $reservationId = $hotel->getReservationId();
        $reservation = $this->reservationRepository->findById($reservationId);
        $reservation->setCompletedState();
        $this->reservationRepository->save($reservation);

        $this->logger->notice(
            "Reservation {$reservation->getId()} was completed successfully"
        );
    }
}
