<?php

declare(strict_types=1);

namespace Users\Infrastructure\User;

use Framework\Infrastructure\AbstractPdoRepository;
use Users\Domain\User\UserRepositoryInterface;

class UserPdoRepository extends AbstractPdoRepository implements UserRepositoryInterface
{
}
