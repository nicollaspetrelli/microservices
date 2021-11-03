<?php

declare(strict_types=1);

namespace Grades\Infrastructure\Persistence\Criteria;

use Grades\Domain\Criteria\Criteria;
use Grades\Domain\Criteria\CriteriaRepository;
use Vcampitelli\Framework\Core\Infrastructure\Persistence\AbstractPdoRepository;

class PdoCriteriaRepository extends AbstractPdoRepository implements CriteriaRepository
{
    /**
     * @param array $row
     *
     * @return Criteria
     */
    protected function buildModel(array $row): Criteria
    {
        return new Criteria($row['id'], $row['name'], $row['weight']);
    }

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return 'criteria';
    }
}
