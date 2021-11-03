<?php

declare(strict_types=1);

namespace Grades\Application\Actions\Grade;

use Grades\Domain\Application\Application;
use Grades\Domain\Application\ApplicationRepository;
use Grades\Domain\Criteria\Criteria;
use Grades\Domain\Criteria\CriteriaRepository;
use Grades\Domain\Grade\Grade;
use Grades\Domain\Grade\GradeRepository;
use Grades\Infrastructure\Notification\NewGradeNotification;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Vcampitelli\Framework\Acl\AclClientInterface;
use Vcampitelli\Framework\Core\Application\Actions\AclAction;
use Vcampitelli\Framework\Core\Domain\DomainException\DomainRecordNotFoundException;
use Vcampitelli\Framework\Notification\Service\NotificationServiceInterface;
use Vcampitelli\Framework\Notification\Service\RabbitMqService;

class CreateGradeAction extends AclAction
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
        private GradeRepository $gradeRepository,
        private NotificationServiceInterface $notificationService
    ) {
        parent::__construct($logger, $acl);
    }

    /**
     * {@inheritdoc}
     * @throws HttpForbiddenException
     */
    protected function action(): Response
    {
        $idApplication = (int) $this->resolveArg('application');
        if ($idApplication < 1) {
            throw new HttpBadRequestException($this->request, 'Invalid Application provided');
        }

        /** @var Application $application */
        $application = $this->applicationRepository->findById($idApplication);
        if (!$this->isResourceAllowed($application)) {
            throw new HttpForbiddenException($this->request);
        }

        return $this->handleData($application);
    }

    /**
     * @param Application $application
     *
     * @return Response
     * @throws HttpBadRequestException
     * @throws DomainRecordNotFoundException
     */
    protected function handleData(Application $application): Response
    {
        $idCriteria = (int) $this->resolveArg('criteria');
        if ($idCriteria < 1) {
            throw new HttpBadRequestException($this->request, 'Invalid Criteria provided');
        }

        $post = $this->getFormData();
        $grade = (float) $post->grade ?? -1;
        if ($grade < 0) {
            throw new HttpBadRequestException($this->request, 'Invalid Grade provided');
        }

        $model = $this->saveGrade($application, $idCriteria, $grade);

        return $this->respondWithData(
            [
                'status' => true,
                'id'     => $model->getId(),
            ]
        );
    }

    /**
     * @param Application $application
     * @param int         $idCriteria
     * @param float       $grade
     *
     * @return Grade
     * @throws DomainRecordNotFoundException
     */
    protected function saveGrade(Application $application, int $idCriteria, float $grade): Grade
    {
        /** @var Criteria $criteria */
        $criteria = $this->criteriaRepository->findById($idCriteria);

        $model = $this->gradeRepository->findOrCreateByApplicationdAndCriteria(
            $application->getId(),
            $criteria->getId()
        );

        $model->setGrade($grade);
        $this->gradeRepository->save($model);

        $this->notify($application, $criteria, $model);

        return $model;
    }

    /**
     * @param Application $application
     * @param Criteria    $criteria
     * @param Grade       $grade
     *
     * @return $this
     */
    protected function notify(Application $application, Criteria $criteria, Grade $grade): self
    {
        $this->notificationService->send(new NewGradeNotification($this->getUser(), $application, $criteria, $grade));

        return $this;
    }
}
