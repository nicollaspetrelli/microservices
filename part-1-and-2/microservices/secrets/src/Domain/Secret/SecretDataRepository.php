<?php
declare(strict_types=1);

namespace Secrets\Domain\Secret;

interface SecretDataRepository
{
    /**
     * @param int $id
     *
     * @return iterable
     */
    public function findAllBySecretId(int $id): iterable;
}
