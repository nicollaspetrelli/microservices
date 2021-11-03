<?php

declare(strict_types=1);

namespace Framework\Core\Infrastructure\Persistence;

use Framework\Core\Domain\ModelInterface;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use Framework\Core\Domain\DomainException\DomainRecordNotFoundException;
use MongoDB\Model\BSONDocument;

abstract class AbstractMongoRepository
{

    /**
     * Pool of found documents
     *
     * @var array
     */
    private array $pool = [];

    /**
     * @param Collection $collection
     */
    public function __construct(private Collection $collection)
    {
    }

    /**
     * @param ModelInterface $model
     */
    public function save(ModelInterface $model): void
    {
        $id = $model->getId();

        $data = $model->toArray();
        unset($data['id']);

        // Insert
        if (empty($id)) {
            $result = $this->collection->insertOne($data);
            $model->setId((string) $result->getInsertedId());
            return;
        }

        // Update
        $this->collection->updateOne(
            ['_id' => new ObjectId($id)],
            ['$set' => $data]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(): array
    {
        $response = [];
        /** @var BSONDocument $document */
        foreach ($this->collection->find() as $document) {
            $id = (string) $document['_id'];
            if (!isset($this->pool[$id])) {
                $data = $document->getArrayCopy();
                $data['id'] = (string) $id;
                unset($data['_id']);
                $this->pool[$id] = $this->buildModel($data);
            }
            $response[$id] = $this->pool[$id];
        }

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ModelInterface
    {
        if (!isset($this->pool[$id])) {
            $document = $this->doFindById($id);
            if (empty($document)) {
                throw new DomainRecordNotFoundException(
                    \get_called_class(),
                    $id
                );
            }

            $data = $document->getArrayCopy();
            $data['id'] = (string) $id;
            unset($data['_id']);
            $this->pool[$id] = $this->buildModel($data);
        }

        return $this->pool[$id];
    }

    /**
     * @param string $id
     *
     * @return BSONDocument|null
     */
    private function doFindById(string $id): ?BSONDocument
    {
        try {
            return $this->collection->findOne(['_id' => new ObjectId($id)]);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * @param array $row
     * @return ModelInterface
     */
    abstract protected function buildModel(array $row): ModelInterface;

}
