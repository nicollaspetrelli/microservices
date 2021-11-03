<?php

declare(strict_types=1);

use Grades\Domain\Application\ApplicationRepository;
use Grades\Domain\Criteria\CriteriaRepository;
use Grades\Domain\Grade\GradeRepository;
use Grades\Infrastructure\Persistence\Application\PdoApplicationRepository;
use Grades\Infrastructure\Persistence\Criteria\PdoCriteriaRepository;
use Grades\Infrastructure\Persistence\Grade\PdoGradeRepository;
use DI\ContainerBuilder;

use function Vcampitelli\Framework\Core\autowireRepository;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our ApplicationRepository interface to its in memory implementation
    $containerBuilder->addDefinitions(
        [
            ApplicationRepository::class => autowireRepository(PdoApplicationRepository::class),
            CriteriaRepository::class => autowireRepository(PdoCriteriaRepository::class),
            GradeRepository::class => autowireRepository(PdoGradeRepository::class),
        ]
    );
};
