<?php

declare(strict_types=1);

namespace Hotel\Domain\Events;

use Framework\Core\Domain\Event\AbstractEvent;
use Flight\Domain\Flight;

class DeniedHotelBookingEvent extends AbstractEvent
{
    /**
     * @param Flight $flight
     * @param string $reason
     */
    public function __construct(Flight $flight, string $reason)
    {
        $this->set('flight', $flight)
            ->set('reason', $reason);
    }

    /**
     * @return \Flight\Domain\Flight
     */
    public function getFlight(): Flight
    {
        return $this->get('flight');
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->get('reason');
    }
}
