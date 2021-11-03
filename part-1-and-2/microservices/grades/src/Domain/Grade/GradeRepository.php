<?php
declare(strict_types=1);

namespace Grades\Domain\Grade;

use Vcampitelli\Framework\Core\Infrastructure\Persistence\RepositoryInterface;

interface GradeRepository extends RepositoryInterface
{
    /**
     * @param int $idApplication
     *
     * @return iterable
     */
    public function findAllByApplication(int $idApplication): iterable;

    /**
     * Finds a model for the specified application and criteria or creates a new model
     *
     * @param int $idApplication
     * @param int $idCriteria
     *
     * @return Grade
     */
    public function findOrCreateByApplicationdAndCriteria(int $idApplication, int $idCriteria): Grade;
}
