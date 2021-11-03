<?php

declare(strict_types=1);

namespace Flight\Domain\Events;

use Framework\Core\Domain\Event\AbstractEvent;
use Flight\Domain\Flight;

final class FlightBookedEvent extends AbstractEvent
{

    /**
     * @param Flight $flight
     */
    public function __construct(Flight $flight)
    {
        if (!$flight->getId()) {
            throw new \InvalidArgumentException('Invalid flight provided');
        }
        if (!$flight->isCompletedState()) {
            throw new \InvalidArgumentException('Invalid flight state provided');
        }

        $this->set('flight', $flight);
    }

    /**
     * @return Flight
     */
    public function getFlight(): Flight
    {
        return $this->get('flight');
    }

}
