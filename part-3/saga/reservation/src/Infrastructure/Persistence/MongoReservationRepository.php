<?php

declare(strict_types=1);

namespace Reservation\Infrastructure\Persistence;

use Framework\Core\Domain\ModelInterface;
use Framework\Core\Infrastructure\Persistence\AbstractMongoRepository;
use Reservation\Domain\Reservation;
use Reservation\Domain\ReservationRepositoryInterface;

class MongoReservationRepository extends AbstractMongoRepository implements ReservationRepositoryInterface
{
    /**
     * @param array $row
     * @return ModelInterface
     */
    protected function buildModel(array $row): ModelInterface
    {
        return Reservation::build($row);
    }
}
