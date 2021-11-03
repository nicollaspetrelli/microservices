<?php

declare(strict_types=1);

namespace Users\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use Users\Application\Commands\RegisterUserCommand;
use Framework\Application\Actions\CommandAction;

class CreateUserAction extends CommandAction
{

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->request->getParsedBody();
        if (empty($data['name'])) {
            throw $this->badRequestException('Missing name field');
        }
        if (empty($data['email'])) {
            throw $this->badRequestException('Missing email field');
        }

        try {
            $command = new RegisterUserCommand(
                $data['name'],
                $data['email']
            );
            $this->dispatchCommand($command);
        } catch (\InvalidArgumentException $exception) {
            throw $this->badRequestException($exception->getMessage());
        }

        $this->getLogger()->info('Creating user ' . $data['name'] . ' (' . $data['email'] . ')');

        return $this->respondWithData([
            'id' => $command->getUuid(),
        ]);
    }

}
