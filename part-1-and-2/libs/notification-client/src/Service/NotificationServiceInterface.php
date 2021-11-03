<?php
declare(strict_types=1);

namespace Vcampitelli\Framework\Notification\Service;

use Vcampitelli\Framework\Notification\NotificationInterface;

interface NotificationServiceInterface
{
    /**
     * Sends a message to the current exchange
     *
     * @param NotificationInterface $message
     *
     * @return NotificationServiceInterface
     */
    public function send(NotificationInterface $message): NotificationServiceInterface;
}
