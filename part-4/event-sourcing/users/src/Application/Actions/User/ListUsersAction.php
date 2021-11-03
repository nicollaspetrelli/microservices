<?php
declare(strict_types=1);

namespace Users\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class ListUsersAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $users = $this->userQuery->findAll();

        $this->getLogger()->info("Users list was viewed.");

        return $this->respondWithData(\iterator_to_array($users));
    }
}
