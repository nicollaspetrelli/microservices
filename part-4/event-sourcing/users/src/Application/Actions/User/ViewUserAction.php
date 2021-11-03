<?php
declare(strict_types=1);

namespace Users\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class ViewUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = (string) $this->resolveArg('id');
        $user = $this->userQuery->findUserOfId($userId);

        $this->getLogger()->info("User of id `${userId}` was viewed.");

        return $this->respondWithData($user);
    }
}
