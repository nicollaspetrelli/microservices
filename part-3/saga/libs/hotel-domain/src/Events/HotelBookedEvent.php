<?php

declare(strict_types=1);

namespace Hotel\Domain\Events;

use Framework\Core\Domain\Event\AbstractEvent;
use Hotel\Domain\Hotel;

final class HotelBookedEvent extends AbstractEvent
{

    /**
     * @param Hotel $hotel
     */
    public function __construct(Hotel $hotel)
    {
        if (!$hotel->getId()) {
            throw new \InvalidArgumentException('Invalid hotel provided');
        }
        if (!$hotel->isCompletedState()) {
            throw new \InvalidArgumentException('Invalid hotel state provided');
        }

        $this->set('hotel', $hotel);
    }

    /**
     * @return Hotel
     */
    public function getHotel(): Hotel
    {
        return $this->get('hotel');
    }

}
