<?php

declare(strict_types=1);

namespace Users\Domain\User\Events;

use Framework\Domain\Event\EventInterface;
use stdClass;
use Users\Domain\User\User;

class UserRegisteredEvent implements EventInterface
{
    /**
     * @param  User  $user
     */
    public function __construct(private User $user)
    {
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param  stdClass  $data
     *
     * @return EventInterface
     */
    public static function jsonUnserialize(stdClass $data): EventInterface
    {
        $user = $data->user;
        return new UserRegisteredEvent(
            new User(
                $user->id,
                $user->name,
                $user->email
            )
        );
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'user' => $this->user,
        ];
    }
}
