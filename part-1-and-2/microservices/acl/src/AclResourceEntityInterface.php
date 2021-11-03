<?php

declare(strict_types=1);

namespace App;

interface AclResourceEntityInterface
{
    /**
     * Returns the ID for the ACL to check if the user has access to this resource. Returning an empty (or null) value
     * means will always deny access to this resource.
     *
     * @return string|null
     */
    public function getAclEntityId(): ?string;
}
