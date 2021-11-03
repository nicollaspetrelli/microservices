<?php

declare(strict_types=1);

namespace Framework\Infrastructure;

use Framework\Domain\Model\WriteModelInterface;

interface RepositoryInterface
{
    /**
     * Persists a model
     */
    public function persist(WriteModelInterface $model): void;
}
