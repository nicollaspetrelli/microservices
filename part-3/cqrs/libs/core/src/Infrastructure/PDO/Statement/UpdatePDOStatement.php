<?php

declare(strict_types=1);

namespace Framework\Infrastructure\PDO\Statement;

class UpdatePDOStatement extends AbstractPDOStatement
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
        $id = $values[$primaryColumn] ?? '';
        unset($values[$primaryColumn]);
        $bind = \array_values($values);

        $sql = 'UPDATE ' . $this->quoteIdentifier($table) . ' SET ';
        $columns = [];
        foreach ($values as $column => $value) {
            $columns[] = $this->quoteIdentifier($column) . ' = ?';
        }
        $sql .= \implode(', ', $columns) . ' WHERE ' . $this->quoteIdentifier($primaryColumn) . ' = ?';
        $bind[] = $id;

        $statement = $this->pdo->prepare($sql);

        if ((!$statement) || (!$statement->execute($bind))) {
            throw new \RuntimeException('Error executing statement');
        }

        return $id;
    }
}
