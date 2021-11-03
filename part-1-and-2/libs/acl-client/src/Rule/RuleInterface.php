<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Acl\Rule;

interface RuleInterface
{

    /**
     * @return string
     */
    public function getResourceName(): string;

    /**
     * @param mixed $resource
     *
     * @return bool
     */
    public function isAllowed(mixed $resource): bool;

}
