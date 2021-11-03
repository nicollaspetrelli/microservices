<?php

declare(strict_types=1);

namespace Secrets\Infrastructure\Persistence\Secret;

use Secrets\Domain\Secret\Secret;
use Secrets\Domain\Secret\SecretDataRepository;
use Secrets\Domain\Secret\SecretNotFoundException;
use Secrets\Domain\Secret\SecretRepository;
use Vcampitelli\Framework\Core\Infrastructure\Persistence\AbstractPdoRepository;

class PdoSecretRepository extends AbstractPdoRepository implements SecretRepository
{
    /**
     * @var SecretDataRepository
     */
    private SecretDataRepository $secretDataRepository;

    /**
     * @var string
     */
    private string $secretKey;

    /**
     * InMemorySecretRepository constructor.
     *
     * @param SecretDataRepository $secretDataRepository
     * @param string               $secretKey
     *
     */
    public function __construct(SecretDataRepository $secretDataRepository, string $secretKey)
    {
        $this->secretDataRepository = $secretDataRepository;
        $this->secretKey = $secretKey;
    }

    /**
     * @param array $row
     *
     * @return Secret
     */
    protected function buildModel(array $row): Secret
    {
        $secret = new Secret($this->secretKey, $row['id'], $row['name']);
        foreach ($this->secretDataRepository->findAllBySecretId($row['id']) as $data) {
            // @TODO re-encrypt with the user secret
            $secret->add($data['name'], $data['value']);
        }
        return $secret;
    }

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return 'secret';
    }
}
