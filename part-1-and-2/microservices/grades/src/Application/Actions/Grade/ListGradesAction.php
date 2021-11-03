<?php
declare(strict_types=1);

namespace Grades\Application\Actions\Grade;

use Grades\Domain\Application\Application;
use Grades\Domain\Application\ApplicationRepository;
use Grades\Domain\Criteria\Criteria;
use Grades\Domain\Criteria\CriteriaRepository;
use Grades\Domain\Grade\Grade;
use Grades\Domain\Grade\GradeRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Vcampitelli\Framework\Acl\AclClientInterface;
use Vcampitelli\Framework\Core\Application\Actions\AclAction;
use Vcampitelli\Framework\Core\Domain\DomainException\DomainRecordNotFoundException;

class ListGradesAction extends AclAction
{
    /**
     * @param LoggerInterface         $logger
     * @param AclClientInterface|null $acl
     * @param ApplicationRepository   $applicationRepository
     * @param CriteriaRepository      $criteriaRepository
     * @param GradeRepository         $gradeRepository
     */
    public function __construct(
        LoggerInterface $logger,
        ?AclClientInterface $acl,
        private ApplicationRepository $applicationRepository,
        private CriteriaRepository $criteriaRepository,
        private GradeRepository $gradeRepository
    ) {
        parent::__construct($logger, $acl);
    }

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->get();

        $this->logger->info("Grades list was viewed.");

        return $this->respondWithData($data);
    }

    /**
     * @return array
     * @throws DomainRecordNotFoundException
     */
    private function get(): array
    {
        /** @var Criteria[] $criterias */
        $criterias = $this->criteriaRepository->findAll();
        if (empty($criterias)) {
            return [];
        }

        $applications = $this->getAllowedResourcesFor(Application::class, $this->applicationRepository);
        if (empty($applications)) {
            return [];
        }

        $response = [];
        $weightsPerApplication = [];
        $weightedGradesPerApplication = [];

        /** @var Application $application */
        foreach ($applications as $application) {
            $idApplication = $application->getId();
            if (!isset($response[$idApplication])) {
                $weightsPerApplication[$idApplication] = 0;
                $weightedGradesPerApplication[$idApplication] = 0;
                $response[$idApplication] = [
                    'id' => $idApplication,
                    'name' => $application->getName(),
                    'grades' => [],
                    'average' => null,
                ];
            }

            $grades = $this->gradeRepository->findAllByApplication($idApplication);

            /** @var Grade $grade */
            foreach ($grades as $grade) {
                $criteria = $criterias[$grade->getIdCriteria()] ?? null;
                if (!isset($criteria)) {
                    continue;
                }

                $weight = $criteria->getWeight();
                $grade = $grade->getGrade();
                $response[$idApplication]['grades'][] = [
                    'criteria' => $criteria->getName(),
                    'weight' => $weight,
                    'grade' => $grade,
                ];
                $weightsPerApplication[$idApplication] += $weight;
                $weightedGradesPerApplication[$idApplication] += $grade * $weight;
            }
        }

        foreach ($response as $idApplication => $row) {
            if ($weightsPerApplication[$idApplication]) {
                $response[$idApplication]['average'] = $weightedGradesPerApplication[$idApplication]
                    / $weightsPerApplication[$idApplication];
            }
        }

        return $response;
    }

}
