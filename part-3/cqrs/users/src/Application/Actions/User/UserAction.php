<?php

declare(strict_types=1);

namespace Users\Application\Actions\User;

use Framework\Application\Actions\Action;
use Psr\Log\LoggerInterface;
use Users\Domain\User\UserQueryInterface;

abstract class UserAction extends Action
{

    /**
     * @param LoggerInterface $logger
     * @param UserQueryInterface $userQuery
     */
    public function __construct(
        LoggerInterface $logger,
        protected UserQueryInterface $userQuery
    ) {
        parent::__construct($logger);
    }

}
