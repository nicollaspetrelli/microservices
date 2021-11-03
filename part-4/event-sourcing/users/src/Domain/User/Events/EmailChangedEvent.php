<?php

declare(strict_types=1);

namespace Users\Domain\User\Events;

use Users\Domain\User\User;

class EmailChangedEvent extends AbstractUserEvent
{
    /**
     * @param  string  $user_id
     * @param  string  $email
     */
    public function __construct(string $user_id, string $email)
    {
        parent::__construct($user_id);
        $this->set('email', $email);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->get('email');
    }

}
