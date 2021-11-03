<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Acl\Rule;

class DeniedAllRule extends AbstractRule
{

    /**
     * @param mixed $resource
     *
     * @return bool
     */
    public function isAllowed(mixed $resource): bool
    {
        return false;
    }

}
