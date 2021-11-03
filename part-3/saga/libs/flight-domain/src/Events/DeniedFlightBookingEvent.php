<?php

declare(strict_types=1);

namespace Flight\Domain\Events;

use Framework\Core\Domain\Event\AbstractEvent;

class DeniedFlightBookingEvent extends AbstractEvent
{
    /**
     * @param string $reservationId
     * @param string $reason
     */
    public function __construct(string $reservationId, string $reason)
    {
        $this->set('reservationId', $reservationId)
            ->set('reason', $reason);
    }

    /**
     * @return string
     */
    public function getReservationId(): string
    {
        return $this->get('reservationId');
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->get('reason');
    }
}
