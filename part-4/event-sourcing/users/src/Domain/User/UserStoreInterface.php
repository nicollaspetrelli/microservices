<?php

declare(strict_types=1);

namespace Users\Domain\User;

use Amp\Promise;
use Users\Domain\User\Events\UserEventInterface;

interface UserStoreInterface
{

    /**
     * Saves the specified user
     *
     * @param  UserEventInterface  $event
     *
     * @return Promise
     */
    public function write(UserEventInterface $event): Promise;

    /**
     * @param  User  $user
     *
     * @return bool
     */
    public function hasStreamForUser(User $user): bool;

}
