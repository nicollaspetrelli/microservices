<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Acl;

use Vcampitelli\Framework\Acl\Rule\RuleInterface;

interface AclClientInterface
{
    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * @param string $role
     * @param mixed $resource
     *
     * @return bool
     */
    public function isAllowed(string $role, mixed $resource): bool;

    /**
     * Returns the resources that the specified role has access to
     *
     * @param string $role
     * @param mixed $resource
     *
     * @return RuleInterface
     */
    public function getRuleForRoleAndResource(string $role, mixed $resource): RuleInterface;
}
