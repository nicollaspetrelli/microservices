<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Core\Application\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Vcampitelli\Framework\Acl\AclClientInterface;
use Vcampitelli\Framework\Acl\AclHttpClientInterface;
use Vcampitelli\Framework\Acl\AclResourceEntityInterface;
use Vcampitelli\Framework\Acl\Rule\AllowedAllRule;
use Vcampitelli\Framework\Acl\Rule\AllowedSomeRule;
use Vcampitelli\Framework\Acl\Rule\DeniedAllRule;
use Vcampitelli\Framework\Core\Domain\User\User;
use Vcampitelli\Framework\Core\Infrastructure\Persistence\RepositoryInterface;

abstract class AclAction extends Action
{

    /**
     * @var AclClientInterface
     */
    protected AclClientInterface $acl;

    /**
     * @var User
     */
    private User $user;

    /**
     * @param LoggerInterface         $logger
     * @param AclClientInterface|null $acl
     */
    public function __construct(LoggerInterface $logger, ?AclClientInterface $acl)
    {
        parent::__construct($logger);
        if ($acl) {
            $this->acl = $acl;
        }
    }

    /**
     * @param array|object|null $data
     * @param int               $statusCode
     *
     * @return Response
     */
    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        if ((isset($this->acl)) && (!empty($data)) && (\is_iterable($data))) {
            $data = $this->filterAclData($data);
        }

        return parent::respondWithData($data, $statusCode);
    }

    /**
     * Method called before the actual action
     */
    protected function preAction(): void
    {
        $user = User::getInstance();
        if (($user) && ($user->getId())) {
            $this->user = $user;
            if ($this->acl instanceof AclHttpClientInterface) {
                $this->acl->setAccessToken($user->getAccessToken());
            }
        }
    }

    /**
     * Filters the specified data to return only resources allowed by the ACL
     *
     * @param iterable $data
     *
     * @return iterable
     */
    protected function filterAclData(iterable $data): iterable
    {
        foreach ($data as $index => $resource) {
            if ((!\is_object($resource)) || (!$resource instanceof AclResourceEntityInterface)) {
                yield $index => $resource;
                continue;
            }

            if ($this->isResourceAllowed($resource)) {
                yield $resource;
            }
        }
    }

    /**
     * Checks if current user has access to the specified resource
     *
     * @param AclResourceEntityInterface $resource
     *
     * @return bool
     */
    protected function isResourceAllowed(AclResourceEntityInterface $resource): bool
    {
        $user = $this->getUser();
        $roles = (isset($user)) ? $user->getRoles() : [];
        if (empty($roles)) {
            return false;
        }

        $acl = $this->acl; // this is more performative
        foreach ($roles as $role) {
            if ($acl->isAllowed($role, $resource)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the allowed resources for the current user
     *
     * @param string                                                                     $resource
     * @param \Vcampitelli\Framework\Core\Infrastructure\Persistence\RepositoryInterface $repository
     *
     * @return array
     */
    protected function getAllowedResourcesFor(string $resource, RepositoryInterface $repository): array
    {
        $user = $this->getUser();
        if (!$user) {
            return [];
        }

        $pool = [];

        foreach ($user->getRoles() as $role) {
            try {
                $rule = $this->acl->getRuleForRoleAndResource($role, $resource);

                // The user has access to every record
                if ($rule instanceof AllowedAllRule) {
                    return $repository->findAll();
                }

                // The user doesn't have access to any records
                if ($rule instanceof DeniedAllRule) {
                    continue;
                }

                if ($rule instanceof AllowedSomeRule) {
                    $pool += $rule->getAllowedIds();
                }
            } catch (\Throwable) {
                continue;
            }
        }

        foreach ($pool as $key => $id) {
            try {
                $pool[$key] = $repository->findById($id);
            } catch (\Throwable) {
                unset($pool[$key]);
            }
        }

        return $pool;
    }

    /**
     * @return User|null
     */
    protected function getUser(): ?User
    {
        return $this->user ?? null;
    }

}
