<?php

declare(strict_types=1);

namespace Framework\Core\Domain;

use Framework\Core\Domain\DomainException\DomainRecordNotFoundException;

interface RepositoryInterface
{

    /**
     * @param ModelInterface $model
     */
    public function save(ModelInterface $model): void;

    /**
     * @return ModelInterface[]
     */
    public function findAll(): array;

    /**
     * @param string $id
     *
     * @return \Framework\Core\Domain\ModelInterface
     * @throws DomainRecordNotFoundException
     */
    public function findById(string $id): ModelInterface;

}
