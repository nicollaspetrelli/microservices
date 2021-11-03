<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Acl;

use GuzzleHttp\Client;
use Vcampitelli\Framework\Acl\Rule\AbstractRule;
use Vcampitelli\Framework\Acl\Rule\AllowedAllRule;
use Vcampitelli\Framework\Acl\Rule\AllowedSomeRule;
use Vcampitelli\Framework\Acl\Rule\DeniedAllRule;
use Vcampitelli\Framework\Acl\Rule\RuleInterface;

class AclHttpClient implements AclHttpClientInterface
{
    /**
     * GuzzleHttp client
     *
     * @var Client
     */
    private Client $httpClient;

    /**
     * Local cache of allowed resources by role
     *
     * @var array
     */
    private array $resourcesByRole = [];

    /**
     * @var string|null
     */
    private ?string $accessToken = null;

    /**
     * AclHttpClient constructor.
     *
     * @param string $baseUri
     */
    public function __construct(string $baseUri)
    {
        $this->httpClient = new Client([
            'base_uri' => $baseUri,
            'timeout' => 3.0,
        ]);
    }

    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * @param string $role
     * @param mixed $resource
     *
     * @return bool
     */
    public function isAllowed(string $role, mixed $resource): bool
    {
        return $this->getRuleForRoleAndResource($role, $resource)->isAllowed($resource);
    }

    /**
     * Returns the Rule for the resources that the role has access to
     *
     * @param string $role
     * @param mixed  $resource
     *
     * @return RuleInterface
     */
    public function getRuleForRoleAndResource(string $role, mixed $resource): RuleInterface
    {
        $return = null;

        [$resource, $parentResource] = $this->serializeResource($resource);
        if (!empty($resource)) {
            try {
                foreach ($this->getRoleResources($role) as $rule) {
                    switch ($rule->getResourceName()) {
                        case $resource:
                        case $parentResource:
                            // We return right away if there's an AllowedAllRule
                            if ($rule instanceof AllowedAllRule) {
                                return $rule;
                            }
                            // We try this AllowedSomeRule but keep looking for a broader one
                            if ($rule instanceof AllowedSomeRule) {
                                $return = $rule;
                            }
                            break;
                    }
                }
            } catch (\Throwable) {
            }
        }

        return $return ?? new DeniedAllRule($resource);
    }

    /**
     * Serializes the resource to be used for querying the ACL
     *
     * @param mixed $resource
     *
     * @return array [<resource-id>, <parent-resource>]
     */
    protected function serializeResource(mixed $resource): array
    {
        if (!\is_object($resource)) {
            return [(string) $resource, null];
        }

        if ($resource instanceof AclResourceEntityInterface) {
            $parentResource = \get_class($resource);
            $id = $resource->getAclEntityId();
            if (empty($id)) {
                return [null, null];
            }
            return ["{$parentResource}:{$id}", $parentResource];
        }

        return [\get_class($resource), null];
    }

    /**
     * Queries the ACL microservice to retrieve the role permissions (first consulting the local cache)
     *
     * @param string $role
     *
     * @return RuleInterface[]
     */
    protected function getRoleResources(string $role): array
    {
        if (!isset($this->resourcesByRole[$role])) {
            $this->resourcesByRole[$role] = $this->doGetRoleResources($role);
        }

        return $this->resourcesByRole[$role];
    }

    /**
     * @param string $role
     *
     * @return array
     */
    protected function doGetRoleResources(string $role): array
    {
        try {
            $response = $this->makeRoleResourcesRequest($role);
        } catch (\Throwable) {
            return [];
        }

        $result = [];
        foreach ($response as $resource) {
            if (!\str_contains($resource, ':')) {
                $result[$resource] = new AllowedAllRule($resource);
                break;
            }

            [$entity, $id] = \explode(':', $resource, 2);
            if ((empty($entity)) || (empty($id))) {
                continue;
            }

            if (!isset($result[$entity])) {
                $result[$entity] = new AllowedSomeRule($entity);
            }
            $result[$entity]->allow($id);
        }
        return $result;
    }

    /**
     * Queries the ACL microservice to retrieve the role permissions
     *
     * @param string $role
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function makeRoleResourcesRequest(string $role): array
    {
        $response = $this->httpClient->get(
            \sprintf(
                '/acl/role/%s/resources',
                \urlencode($role)
            ),
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getCurrentAccessToken(),
                ],
            ]
        );

        if ($response->getStatusCode() !== 200) {
            // @TODO logging
            return [];
        }

        $response = (string) $response->getBody();
        if (empty($response)) {
            // @TODO logging
            return [];
        }

        $response = \json_decode($response);
        if ((empty($response)) || (empty($response->resources))) {
            // @TODO logging
            return [];
        }

        return $response->resources;
    }

    /**
     * Sets the access token
     *
     * @param string $token
     *
     * @return AclHttpClientInterface
     */
    public function setAccessToken(string $token): AclHttpClientInterface
    {
        $this->accessToken = $token;
        return $this;
    }

    /**
     * @return string|null
     */
    private function getCurrentAccessToken(): ?string
    {
        return $this->accessToken;
    }
}
