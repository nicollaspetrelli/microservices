<?php

declare(strict_types=1);

namespace Framework\Infrastructure\PDO\Statement;

use Framework\Domain\Model\WriteModelInterface;
use PDO;
use PDOStatement;

abstract class AbstractPDOStatement
{

    /**
     * @param  PDO  $pdo
     */
    public function __construct(protected PDO $pdo)
    {
    }

    /**
     * @param  string  $column
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
}
