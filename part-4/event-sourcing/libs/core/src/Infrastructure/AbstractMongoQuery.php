<?php

declare(strict_types=1);

namespace Framework\Infrastructure;

use ArrayObject;
use Framework\Domain\DomainException\DomainRecordNotFoundException;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;

abstract class AbstractMongoQuery
{

    /**
     * @param Collection $collection
     */
    public function __construct(
        private Collection $collection
    ) {
    }

    /**
     * Returns all objects
     *
     * @return object[]
     */
    public function findAll(): iterable
    {
        foreach ($this->getCollection()->find() as $document) {
            /** @var BSONDocument $document */
            yield $this->toModel($document);
        }
    }

    /**
     * @param string $id
     * @return object
     * @throws DomainRecordNotFoundException
     */
    public function findById(string $id): object
    {
        /** @var BSONDocument $document */
        $document = $this->getCollection()->findOne(['_id' => $id]);
        if (!$document) {
            throw new DomainRecordNotFoundException();
        }

        return $this->toModel($document);
    }

    /**
     * @param string $column
     * @param $value
     * @return object
     * @throws DomainRecordNotFoundException
     */
    protected function findBy(string $column, $value): object
    {
        /** @var BSONDocument $document */
        $document = $this->getCollection()->findOne([$column => $value]);
        if (!$document) {
            throw new DomainRecordNotFoundException();
        }
        return $this->toModel($document);
    }

    /**
     * @return Collection
     */
    protected function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * @param ArrayObject $document
     * @return object
     */
    abstract protected function toModel(ArrayObject $document): object;
}
