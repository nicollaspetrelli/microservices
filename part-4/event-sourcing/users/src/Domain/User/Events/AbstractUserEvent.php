<?php

declare(strict_types=1);

namespace Users\Domain\User\Events;

use Framework\Domain\Event\AbstractEvent;
use Users\Domain\User\User;

abstract class AbstractUserEvent extends AbstractEvent implements UserEventInterface
{

    /**
     * @param  string  $user_id
     */
    public function __construct(string $user_id)
    {
        $this->set('user_id', $user_id);
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->get('user_id');
    }

}
