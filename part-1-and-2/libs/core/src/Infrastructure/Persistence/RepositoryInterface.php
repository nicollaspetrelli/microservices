<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Core\Infrastructure\Persistence;

use Vcampitelli\Framework\Core\Domain\DomainException\DomainRecordNotFoundException;
use Vcampitelli\Framework\Core\Domain\ModelInterface;

interface RepositoryInterface
{
    /**
     * @param ModelInterface $model
     *
     * @return int Generated ID
     */
    public function save(ModelInterface $model): int;

    /**
     * @return ModelInterface[]
     */
    public function findAll(): array;

    /**
     * @param $id
     *
     * @return ModelInterface
     * @throws DomainRecordNotFoundException
     */
    public function findById($id): ModelInterface;
}
