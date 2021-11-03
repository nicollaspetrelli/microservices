<?php

declare(strict_types=1);

namespace Grades\Infrastructure\Persistence\Grade;

use Grades\Domain\Grade\Grade;
use Grades\Domain\Grade\GradeRepository;
use Vcampitelli\Framework\Core\Infrastructure\Persistence\AbstractPdoRepository;

class PdoGradeRepository extends AbstractPdoRepository implements GradeRepository
{
    /**
     * @param int $idApplication
     *
     * @return iterable
     */
    public function findAllByApplication(int $idApplication): iterable
    {
        foreach ($this->fetchWhereFromPdo(['id_application = ?' => $idApplication]) as $row) {
            yield $this->buildModel($row);
        }
    }

    /**
     * Finds a model for the specified application and criteria or creates a new model
     *
     * @param int $idApplication
     * @param int $idCriteria
     *
     * @return Grade
     */
    public function findOrCreateByApplicationdAndCriteria(int $idApplication, int $idCriteria): Grade
    {
        foreach ($this->fetchWhereFromPdo(
            [
                'id_application = ?' => $idApplication,
                'id_criteria = ?'    => $idCriteria,
            ],
            1
        ) as $row) {
            return $this->buildModel($row);
        }

        return new Grade(
            null,
            $idApplication,
            $idCriteria
        );
    }

    /**
     * @param array $row
     *
     * @return Grade
     */
    protected function buildModel(array $row): Grade
    {
        return new Grade(
            $row['id'],
            $row['id_application'],
            $row['id_criteria'],
            (float) $row['grade']
        );
    }

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return 'grade';
    }
}
