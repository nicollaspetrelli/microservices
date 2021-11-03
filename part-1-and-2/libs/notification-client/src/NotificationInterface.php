<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Notification;

use Vcampitelli\Framework\Core\Domain\User\User;

interface NotificationInterface
{
    /**
     * @return User
     */
    public function getUser(): User;

    /**
     * @return string
     */
    public function getSubject(): string;

    /**
     * @return string
     */
    public function getBody(): string;
}
