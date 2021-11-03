<?php

declare(strict_types=1);

namespace Users\Domain\User\Events;

use Framework\Domain\Event\AbstractEvent;
use Users\Domain\User\User;

class UserRegisteredEvent extends AbstractEvent implements UserEventInterface
{
    /**
     * @param  User  $user
     */
    public function __construct(User $user)
    {
        $this->set('user', $user);
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->get('user');
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->getUser()->getId();
    }

}
