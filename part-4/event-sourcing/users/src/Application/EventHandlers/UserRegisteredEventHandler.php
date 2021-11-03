<?php

declare(strict_types=1);

namespace Users\Application\EventHandlers;

use Framework\Application\EventHandlers\AbstractReadModelUpdateEventHandler;
use Users\Domain\User\Events\UserRegisteredEvent;

class UserRegisteredEventHandler extends AbstractReadModelUpdateEventHandler
{
    public function __invoke(UserRegisteredEvent $event)
    {
        // @TODO aggregate
        $user = $event->getUser();
        $data = $user->jsonSerialize();
        unset($data['id']);
        $this->updateReadModel(
            $user->getId(),
            $data
        );
    }
}
