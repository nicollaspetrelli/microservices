<?php

declare(strict_types=1);

namespace Users\Application\Actions\User;

use Framework\Application\Actions\Action;
use Psr\Log\LoggerInterface;
use Users\Domain\User\UserQueryInterface;
use Users\Domain\User\UserStoreInterface;

abstract class UserAction extends Action
{

    /**
     * @param LoggerInterface $logger
     * @param UserQueryInterface $userQuery
     * @param UserStoreInterface $userStore
     */
    public function __construct(
        LoggerInterface $logger,
        protected UserQueryInterface $userQuery,
        protected UserStoreInterface $userStore
    ) {
        parent::__construct($logger);
    }

}
