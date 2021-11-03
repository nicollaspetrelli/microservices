<?php

declare(strict_types=1);

namespace App\LaminasAcl;

use Laminas\Permissions\Acl\Acl;
use App\AclInterface;

class LaminasAcl implements AclInterface
{
    /**
     * @var Acl
     */
    private Acl $acl;

    /**
     * @var array
     */
    protected array $allowedResourcesPerRole = [];

    /**
     * LaminasAcl constructor.
     */
    public function __construct()
    {
        $this->acl = new Acl();
    }

    /**
     * Adds a role
     *
     * @param string $role
     *
     * @return AclInterface
     */
    public function addRole(string $role): AclInterface
    {
        $this->acl->addRole($role);
        return $this;
    }

    /**
     * Adds a resource
     *
     * @param string $resource
     *
     * @return AclInterface
     */
    public function addResource(string $resource): AclInterface
    {
        $this->acl->addResource($resource);
        return $this;
    }

    /**
     * Adds an "allow" rule
     *
     * @param string $role
     * @param string $resource
     * @param string|null $resourceId
     *
     * @return AclInterface
     */
    public function allow(string $role, string $resource, string $resourceId = null): AclInterface
    {
        if (!$this->acl->hasResource($resource)) {
            $this->acl->addResource($resource);
        }

        $serialized = $this->serialize($resource, $resourceId);

        if ((isset($resourceId)) && (!$this->acl->hasResource($serialized))) {
            $this->acl->addResource($serialized);
        }

        $this->acl->allow($role, $serialized);

        return $this;
    }

    /**
     * Adds a "deny" rule
     *
     * @param array|string|null $role
     * @param array|string|null $resource
     *
     * @return AclInterface
     */
    public function deny($role = null, $resource = null): AclInterface
    {
        $this->acl->deny($role, $resource);
        return $this;
    }

    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * @param string $role
     * @param string $resource
     * @param string|null $resourceId
     *
     * @return bool
     */
    public function isAllowed(string $role, string $resource, string $resourceId = null): bool
    {
        return $this->acl->isAllowed($role, $this->serialize($resource, $resourceId));
    }

    /**
     * Returns the allowed resources for the specified role
     *
     * @param string $role
     * @return array
     */
    public function getAllowedResources(string $role): array
    {
        if (!isset($this->allowedResourcesPerRole[$role])) {
            $this->allowedResourcesPerRole[$role] = $this->doGetAllowedResources($role);
        }
        return $this->allowedResourcesPerRole[$role];
    }

    /**
     * @param string $role
     * @return array
     */
    protected function doGetAllowedResources(string $role): array
    {
        if (!$this->acl->hasRole($role)) {
            return [];
        }

        $response = [];
        foreach ($this->acl->getResources() as $resource) {
            if ($this->acl->isAllowed($role, $resource)) {
                $response[] = $resource;
            }
        }

        return $response;
    }

    /**
     * @param string $resource
     * @param string|null $resourceId
     * @return string
     */
    protected function serialize(string $resource, string $resourceId = null): string
    {
        return (isset($resourceId)) ? "{$resource}:{$resourceId}" : $resource;
    }
}
