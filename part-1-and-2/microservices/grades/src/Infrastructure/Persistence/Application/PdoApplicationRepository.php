<?php

declare(strict_types=1);

namespace Grades\Infrastructure\Persistence\Application;

use Grades\Domain\Application\Application;
use Grades\Domain\Application\ApplicationRepository;
use Vcampitelli\Framework\Core\Infrastructure\Persistence\AbstractPdoRepository;

class PdoApplicationRepository extends AbstractPdoRepository implements ApplicationRepository
{
    /**
     * @param array $row
     *
     * @return Application
     */
    protected function buildModel(array $row): Application
    {
        return new Application($row['id'], $row['name']);
    }

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return 'application';
    }
}
