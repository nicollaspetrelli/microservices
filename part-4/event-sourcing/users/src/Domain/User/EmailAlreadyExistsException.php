<?php

declare(strict_types=1);

namespace Users\Domain\User;

use Framework\Domain\DomainException\DomainRecordNotFoundException;

class EmailAlreadyExistsException extends DomainRecordNotFoundException
{

    /**
     * @param string          $email
     * @param int             $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $email, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(
          "There's already a user registered for the email {$email}",
          0,
          $previous
        );
    }

}
