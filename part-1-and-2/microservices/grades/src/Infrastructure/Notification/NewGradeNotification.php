<?php

declare(strict_types=1);

namespace Grades\Infrastructure\Notification;

use Grades\Domain\Application\Application;
use Grades\Domain\Criteria\Criteria;
use Grades\Domain\Grade\Grade;
use Vcampitelli\Framework\Core\Domain\User\User;
use Vcampitelli\Framework\Notification\NotificationInterface;

class NewGradeNotification implements NotificationInterface
{
    /**
     * NewGradeNotification constructor.
     *
     * @param User        $user
     * @param Application $application
     * @param Criteria    $criteria
     * @param Grade       $grade
     */
    public function __construct(
        private User $user,
        private Application $application,
        private Criteria $criteria,
        private Grade $grade
    ) {
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return 'Nova notificação';
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return \sprintf(
            'Você recebeu a nota %.2f no critério "%s" para a aplicação "%s"',
            $this->grade->getGrade(),
            $this->criteria->getName(),
            $this->application->getName(),
        );
    }
}
