<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Acl\Rule;

use Vcampitelli\Framework\Acl\AclResourceEntityInterface;

class AllowedSomeRule extends AbstractRule
{

    /**
     * @var bool[]
     */
    private array $rulePerId = [];

    /**
     * Allows a single ID
     *
     * @param string $id
     *
     * @return $this
     */
    public function allow(string $id): self
    {
        $this->rulePerId[$id] = true;
        return $this;
    }

    /**
     * Denies a single ID
     *
     * @param string $id
     *
     * @return $this
     */
    public function deny(string $id): self
    {
        $this->rulePerId[$id] = false;
        return $this;
    }

    /**
     * @param mixed $resource
     *
     * @return bool
     */
    public function isAllowed(mixed $resource): bool
    {
        if (!\is_object($resource)) {
            return false;
        }

        if (\get_class($resource) !== $this->getResourceName()) {
            return false;
        }

        if (!$resource instanceof AclResourceEntityInterface) {
            return false;
        }

        return $this->rulePerId[$resource->getAclEntityId()] ?? false;
    }

    /**
     * @return array
     */
    public function getAllowedIds(): array
    {
        $response = [];
        foreach ($this->rulePerId as $id => $rule) {
            if ($rule === true) {
                $response[$id] = $id;
            }
        }
        return $response;
    }

}
