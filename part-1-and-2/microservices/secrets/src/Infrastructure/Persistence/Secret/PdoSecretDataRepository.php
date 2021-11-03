<?php

declare(strict_types=1);

namespace Secrets\Infrastructure\Persistence\Secret;

use Secrets\Domain\Secret\Secret;
use Secrets\Domain\Secret\SecretDataRepository;
use Vcampitelli\Framework\Core\Infrastructure\Persistence\AbstractPdoRepository;

class PdoSecretDataRepository extends AbstractPdoRepository implements SecretDataRepository
{
    /**
     * @param int $id
     *
     * @return iterable
     */
    public function findAllBySecretId(int $id): iterable
    {
        foreach ($this->fetchAllFromPdo() as $row) {
            if (((int) $row['id_secret']) === $id) {
                yield $row;
            }
        }
    }

    /**
     * @param array $row
     *
     * @return object
     */
    protected function buildModel(array $row): object
    {
        return new \stdClass();
    }

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return 'secret_data';
    }
}
