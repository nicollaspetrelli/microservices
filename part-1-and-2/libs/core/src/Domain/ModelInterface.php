<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Core\Domain;

use JsonSerializable;
use Vcampitelli\Framework\Acl\AclResourceEntityInterface;

interface ModelInterface extends AclResourceEntityInterface, JsonSerializable
{
    /**
     * @param int $id
     *
     * @return ModelInterface
     */
    public function setId(int $id): ModelInterface;

    /**
     * @return int|null
     */
    public function getId(): ?int;
}
