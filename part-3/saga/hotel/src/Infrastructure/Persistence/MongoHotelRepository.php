<?php

declare(strict_types=1);

namespace Hotel\Infrastructure\Persistence;

use Framework\Core\Domain\ModelInterface;
use Framework\Core\Infrastructure\Persistence\AbstractMongoRepository;
use Flight\Domain\Flight;
use Hotel\Domain\HotelRepositoryInterface;

class MongoHotelRepository extends AbstractMongoRepository implements HotelRepositoryInterface
{
    /**
     * @param array $row
     * @return ModelInterface
     */
    protected function buildModel(array $row): ModelInterface
    {
        return Flight::build($row);
    }
}
