<?php
declare(strict_types=1);

namespace Users\Domain\User;

use Framework\Domain\DomainException\DomainRecordNotFoundException;

class UserNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The user you requested does not exist.';
}
