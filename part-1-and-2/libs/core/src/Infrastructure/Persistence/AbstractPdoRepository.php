<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Core\Infrastructure\Persistence;

use PDO;
use Vcampitelli\Framework\Core\Domain\DomainException\DomainRecordNotFoundException;
use Vcampitelli\Framework\Core\Domain\ModelInterface;

abstract class AbstractPdoRepository implements RepositoryInterface
{
    /**
     * @var PDO
     */
    private PDO $pdo;

    /**
     * @var array
     */
    private array $allRows;

    /**
     * @var array
     */
    protected array $pool = [];

    /**
     * @param ModelInterface $model
     *
     * @return int Generated ID
     */
    public function save(ModelInterface $model): int
    {
        $id = $model->getId();

        [$query, $bind] = $this->buildSaveStatement($model);

        $statement = $this->pdo->prepare($query);
        if ((!$statement) || (!$statement->execute($bind))) {
            throw new \RuntimeException("Error saving " . \get_class($model));
        }

        if (!$id) {
            $id = (int) $this->pdo->lastInsertId();
            if ($id < 1) {
                throw new \RuntimeException("Error saving " . \get_class($model));
            }
            $model->setId($id);
        }

        $this->pool[$id] = $model;

        return $id;
    }

    /**
     * @param ModelInterface $model
     *
     * @return array
     */
    private function buildSaveStatement(ModelInterface $model): array
    {
        $id = $model->getId();

        $values = $model->jsonSerialize();
        if (empty($values)) {
            throw new \UnexpectedValueException();
        }

        $primaryColumn = $this->getPrimaryColumn();
        unset($values[$primaryColumn]);
        $bind = \array_values($values);

        // Updating
        if ($id) {
            $sql = 'UPDATE ' . $this->quoteIdentifier($this->getTableName()) . ' SET ';
            $columns = [];
            foreach ($values as $column => $value) {
                $columns[] = $this->quoteIdentifier($column) . ' = ?';
            }
            $sql .= \implode(', ', $columns) . ' WHERE ' . $this->quoteIdentifier($primaryColumn) . ' = ?';
            $bind[] = $id;

            return [$sql, $bind];
        }

        // Creating
        $sql = \sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->quoteIdentifier($this->getTableName()),
            implode(', ', \array_map([$this, 'quoteIdentifier'], \array_keys($values))),
            \rtrim(\str_repeat('?,', count($values)), ',')
        );
        return [$sql, $bind];
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $response = [];
        $primaryColumn = $this->getPrimaryColumn();
        foreach ($this->fetchAllFromPdo() as $row) {
            if (!isset($this->pool[$row[$primaryColumn]])) {
                $this->pool[$row[$primaryColumn]] = $this->buildModel($row);
            }
            $response[$row[$primaryColumn]] = $this->pool[$row[$primaryColumn]];
        }
        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function findById($id): ModelInterface
    {
        if (!isset($this->pool[$id])) {
            $row = $this->fetchByIdFromPdo($id);
            if (empty($row)) {
                throw new DomainRecordNotFoundException(
                    $this->getTableName(),
                    $id
                );
            }

            $this->pool[$id] = $this->buildModel($row);
        }

        return $this->pool[$id];
    }

    /**
     * @param array $settings
     */
    public function setPdo(array $settings)
    {
        $this->pdo = new PDO(
            $settings['dsn'],
            $settings['username'] ?? null,
            $settings['password'] ?? null,
            $settings['options'] ?? null
        );
    }

    /**
     * @param $id
     *
     * @return array|null
     */
    protected function fetchByIdFromPdo($id): ?array
    {
        $row = $this->fetchOneWhere($this->getPrimaryColumn(), $id);
        if (empty($row)) {
            // @TODO logging
            return null;
        }

        return $row;
    }

    /**
     * @return array|null
     */
    protected function fetchAllFromPdo(): ?array
    {
        if (!isset($this->allRows)) {
            $this->allRows = [];
            $query = $this->pdo->query('SELECT * FROM ' . $this->quoteIdentifier($this->getTableName()));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $this->allRows[] = $row;
            }
        }
        return $this->allRows;
    }

    /**
     * @param array $criteria
     * @param int   $limit
     *
     * @return iterable
     */
    protected function fetchWhereFromPdo(array $criteria, int $limit = 0): iterable
    {
        // Querying the database
        $sql = 'SELECT * FROM ' . $this->quoteIdentifier($this->getTableName()) .
            ' WHERE ' . \implode(' AND ', \array_keys($criteria));
        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
        }
        $statement = $this->pdo->prepare($sql);
        if (!$statement->execute(\array_values($criteria))) {
            // @TODO logging
            return;
        }

        $primaryColumn = $this->getPrimaryColumn();
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            if ((isset($row[$primaryColumn])) && (!isset($this->pool[$row[$primaryColumn]]))) {
                $this->pool[$row[$primaryColumn]] = $row;
            }
            yield $row;
        }
    }

    /**
     * @param string $column
     * @param        $value
     *
     * @return array|null
     */
    private function fetchOneWhere(string $column, $value): ?array
    {
        $statement = $this->pdo->prepare(
            \sprintf(
                'SELECT * FROM %s WHERE %s = ? LIMIT 1',
                $this->quoteIdentifier($this->getTableName()),
                $this->quoteIdentifier($column)
            )
        );
        if (!$statement->execute([$value])) {
            // @TODO logging
            return null;
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * @param string $column
     *
     * @return string
     */
    protected function quoteIdentifier(string $column): string
    {
        switch ($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            case 'pgsql':
                $char = '"';
                $quote = '""';
                break;

            case 'mysql':
                $char = '`';
                $quote = '`';
                break;

            default:
                return $this->pdo->quote($column);
        }

        return $char . str_replace($char, $quote, $column) . $char;
    }

    /**
     * @return string
     */
    protected function getPrimaryColumn(): string
    {
        return 'id';
    }

    /**
     * @return string
     */
    abstract protected function getTableName(): string;

    /**
     * @param array $row
     *
     * @return object
     */
    abstract protected function buildModel(array $row): object;
}
