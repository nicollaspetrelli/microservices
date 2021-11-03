<?php

declare(strict_types=1);

namespace Users\Infrastructure\User;

use ArrayObject;
use Framework\Domain\DomainException\DomainRecordNotFoundException;
use Framework\Infrastructure\AbstractMongoQuery;
use Users\Domain\User\User;
use Users\Domain\User\UserNotFoundException;
use Users\Domain\User\UserQueryInterface;

class UserMongoQuery extends AbstractMongoQuery implements UserQueryInterface
{

    /**
     * {@inheritdoc}
     */
    public function findUserOfId(string $id): User
    {
        try {
            return $this->findById($id);
        } catch (DomainRecordNotFoundException) {
            throw new UserNotFoundException();
        }
    }

    /**
     * @param string $email
     *
     * @return User
     * @throws UserNotFoundException
     */
    public function findByEmail(string $email): User
    {
        try {
            return $this->findBy('email', $email);
        } catch (DomainRecordNotFoundException) {
            throw new UserNotFoundException();
        }
    }

    /**
     * @param ArrayObject $document
     * @return User
     */
    protected function toModel(ArrayObject $document): User
    {
        $document = $document->getArrayCopy();

        return new User(
            (string) $document['_id'],
            $document['name'],
            $document['email']
        );
    }

}
