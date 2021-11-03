<?php

declare(strict_types=1);

namespace Reservation\Domain\Events;

use Framework\Core\Domain\Event\AbstractEvent;
use Reservation\Domain\Reservation;

final class PendingReservationCreatedEvent extends AbstractEvent
{

    /**
     * @param Reservation $reservation
     */
    public function __construct(Reservation $reservation)
    {
        if (!$reservation->getId()) {
            throw new \InvalidArgumentException('Invalid reservation provided');
        }
        if (!$reservation->isPendingState()) {
            throw new \InvalidArgumentException('Invalid reservation state provided');
        }

        $this->set('reservation', $reservation);
    }

    /**
     * @return Reservation
     */
    public function getReservation(): Reservation
    {
        return $this->get('reservation');
    }

}
