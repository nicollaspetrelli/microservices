<?php

declare(strict_types=1);

namespace Flight\Infrastructure\Persistence;

use Framework\Core\Domain\ModelInterface;
use Framework\Core\Infrastructure\Persistence\AbstractMongoRepository;
use Flight\Domain\Flight;
use Flight\Domain\FlightRepositoryInterface;

class MongoFlightRepository extends AbstractMongoRepository implements FlightRepositoryInterface
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
