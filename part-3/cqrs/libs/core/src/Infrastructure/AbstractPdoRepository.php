<?php

declare(strict_types=1);

namespace Framework\Infrastructure;

use Framework\Domain\Model\WriteModelInterface;
use Framework\Infrastructure\PDO\Statement\InsertPDOStatement;
use Framework\Infrastructure\PDO\Statement\UpdatePDOStatement;
use PDO;

abstract class AbstractPdoRepository implements RepositoryInterface
{
    /**
     * @param  PDO     $pdo
     * @param  string  $tableName
     * @param  string  $primaryColumn
     */
    public function __construct(
        private PDO $pdo,
        private string $tableName,
        private string $primaryColumn = 'id'
    ) {
    }

    /**
     * @return PDO
     */
    protected function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * {@inheritDoc}
     */
    public function persist(WriteModelInterface $model): void
    {
        $values = $model->getPersistValues();
        if (empty($values)) {
            throw new \UnexpectedValueException();
        }

        $id = $model->getId();

        $statement = (empty($id))
            ? new InsertPDOStatement($this->getPdo())
            : new UpdatePDOStatement($this->getPdo());

        try {
            $generatedId = $statement($values, $this->tableName, $this->primaryColumn);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error persisting ' . \get_class($model) . ': ' . $e->getMessage());
        }

        if (!$id) {
            $model->setId($generatedId);
        }
    }

}
