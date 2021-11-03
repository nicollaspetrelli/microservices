<?php

declare(strict_types=1);

namespace Framework\Infrastructure\PDO\Statement;

class InsertPDOStatement extends AbstractPDOStatement
{
    /**
     * @param  array   $values
     * @param  string  $table
     * @param  string  $primaryColumn
     *
     * @return string
     */
    public function __invoke(array $values, string $table, string $primaryColumn = 'id'): string
    {
        unset($values[$primaryColumn]);

        $query = \sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->quoteIdentifier($table),
            implode(', ', \array_map([$this, 'quoteIdentifier'], \array_keys($values))),
            \rtrim(\str_repeat('?,', count($values)), ',')
        );

        $statement = $this->pdo->prepare($query);
        if ((!$statement) || (!$statement->execute(\array_values($values)))) {
            throw new \RuntimeException('Error executing statement');
        }

        $id = $this->pdo->lastInsertId();
        if (empty($id)) {
            throw new \RuntimeException('Error retrieving last inserted ID');
        }

        return $id;
    }
}
