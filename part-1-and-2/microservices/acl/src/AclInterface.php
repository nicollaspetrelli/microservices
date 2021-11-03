<?php

declare(strict_types=1);

namespace App;

interface AclInterface
{
    /**
     * Adds a role
     *
     * @param string $role
     *
     * @return AclInterface
     */
    public function addRole(string $role): AclInterface;

    /**
     * Adds a resource
     *
     * @param string $resource
     *
     * @return AclInterface
     */
    public function addResource(string $resource): AclInterface;

    /**
     * Adds an "allow" rule
     *
     * @param string $role
     * @param string $resource
     * @param string|null $resourceId
     *
     * @return AclInterface
     */
    public function allow(string $role, string $resource, string $resourceId = null): AclInterface;

    /**
     * Adds a "deny" rule
     *
     * @param string $role
     * @param string $resource
     *
     * @return AclInterface
     */
    public function deny(string $role, string $resource): AclInterface;

    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * @param string $role
     * @param string $resource
     * @param string|null $resourceId
     *
     * @return bool
     */
    public function isAllowed(string $role, string $resource, string $resourceId = null): bool;

    /**
     * Returns the allowed resources for the specified role
     *
     * @param string $role
     * @return array
     */
    public function getAllowedResources(string $role): array;
}
