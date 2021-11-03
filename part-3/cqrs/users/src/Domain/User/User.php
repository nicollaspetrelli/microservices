<?php

declare(strict_types=1);

namespace Users\Domain\User;

use Framework\Domain\Model\AbstractWriteModel;

class User extends AbstractWriteModel
{

    /**
     * @param string|null $id
     * @param string   $name
     * @param string   $email
     */
    public function __construct(
      ?string $id,
      private string $name,
      private string $email
    ) {
        parent::__construct($id);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
          'id'    => $this->getId(),
          'name'  => $this->name,
          'email' => $this->email,
        ];
    }

}
