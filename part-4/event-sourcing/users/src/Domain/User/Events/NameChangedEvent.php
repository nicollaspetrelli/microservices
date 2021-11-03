<?php

declare(strict_types=1);

namespace Users\Domain\User\Events;

class NameChangedEvent extends AbstractUserEvent
{
    /**
     * @param  string  $user_id
     * @param  string  $name
     */
    public function __construct(string $user_id, string $name)
    {
        parent::__construct($user_id);
        $this->set('name', $name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->get('name');
    }

}
