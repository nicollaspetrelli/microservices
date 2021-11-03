<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Acl\Rule;

abstract class AbstractRule implements RuleInterface
{

    /**
     * AllowedResource constructor.
     *
     * @param string $resource
     */
    public function __construct(private string $resource)
    {
    }

    /**
     * @return string
     */
    public function getResourceName(): string
    {
        return $this->resource;
    }

}
