<?php

declare(strict_types=1);

namespace Users\Domain\User;

interface UserQueryInterface
{

    /**
     * @return User[]
     */
    public function findAll(): iterable;

    /**
     * @param string $id
     *
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfId(string $id): User;

    /**
     * @param string $email
     *
     * @return User
     * @throws UserNotFoundException
     */
    public function findByEmail(string $email): User;

}
