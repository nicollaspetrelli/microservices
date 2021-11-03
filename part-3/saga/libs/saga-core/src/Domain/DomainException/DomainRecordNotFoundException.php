<?php
declare(strict_types=1);

namespace Framework\Core\Domain\DomainException;

class DomainRecordNotFoundException extends DomainException
{
    /**
     * DomainRecordNotFoundException constructor.
     *
     * @param string     $class
     * @param string|int $id
     */
    public function __construct(string $class, $id)
    {
        parent::__construct(
            \ucfirst($class) . " #{$id} not found"
        );
    }
}
