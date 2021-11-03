<?php

declare(strict_types=1);

namespace Users\Domain\User\Events;

use Framework\Domain\Event\EventInterface;

interface UserEventInterface extends EventInterface
{

    /**
     * @return string
     */
    public function getUserId(): string;
}
