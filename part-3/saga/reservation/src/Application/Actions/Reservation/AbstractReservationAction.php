<?php

declare(strict_types=1);

namespace Reservation\Application\Actions\Reservation;

use Framework\Http\Application\Actions\Action;
use Framework\Core\Application\Service\EventBrokerInterface;
use Psr\Log\LoggerInterface;
use Reservation\Domain\ReservationRepositoryInterface;

abstract class AbstractReservationAction extends Action
{

    /**
     * @param LoggerInterface $logger
     * @param EventBrokerInterface $broker
     * @param ReservationRepositoryInterface $reservationRepository
     */
    public function __construct(
        LoggerInterface $logger,
        EventBrokerInterface $broker,
        protected ReservationRepositoryInterface $reservationRepository
    ) {
        parent::__construct($logger, $broker);
    }

}
