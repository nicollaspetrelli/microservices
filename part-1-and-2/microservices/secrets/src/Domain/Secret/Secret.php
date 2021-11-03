<?php
/**
 * @author Vinícius Campitelli <eu@viniciuscampitelli.com>
 */

declare(strict_types=1);

namespace Secrets\Domain\Secret;

use JsonSerializable;
use Vcampitelli\Framework\Acl\AclResourceEntityInterface;
use Vcampitelli\Framework\Core\Domain\ModelInterface;

/**
 * Class that represents a Secret
 *
 * @package Secrets\Domain\Secret
 */
class Secret implements ModelInterface
{
    /**
     * @var int|null
     */
    private int $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var array
     */
    private array $data = [];

    /**
     * Secret key
     *
     * @var string
     */
    private string $secretKey;


    /**
     * @param string      $secretKey
     * @param int|null    $id
     * @param string|null $name
     */
    public function __construct(string $secretKey, int $id = null, string $name = null)
    {
        $this->secretKey = $secretKey;
        if ($id) {
            $this->setId($id);
        }
        if ($name) {
            $this->name = $name;
        }
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Adds an encrypted value
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     * @throws \Exception
     */
    public function addEncrypted(string $key, string $value): self
    {
        $this->data[$key] = $this->encrypt($key, $value);
        return $this;
    }

    /**
     * Encrypts a value
     *
     * @param string $key
     * @param string $value
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function encrypt(string $key, string $value): string
    {
        $nonce = \random_bytes(24);
        return \base64_encode(
            $nonce . \sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
                $value,
                $this->buildAuthenticatedData($key),
                $nonce,
                $this->secretKey
            )
        );
    }

    /**
     * Decrypts a value
     *
     * @param string $key
     * @param string $message
     *
     * @return string
     * @throws \SodiumException
     */
    protected function decrypt(string $key, string $message): string
    {
        $message = \base64_decode($message);
        if (empty($message)) {
            throw new \UnexpectedValueException('Invalid message');
        }

        $nonce = \mb_substr($message, 0, 24, '8bit');
        $ciphertext = \mb_substr($message, 24, null, '8bit');
        if (empty($ciphertext)) {
            throw new \UnexpectedValueException('Invalid message');
        }

        $plaintext = \sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
            $ciphertext,
            $this->buildAuthenticatedData($key),
            $nonce,
            $this->secretKey
        );
        if (!\is_string($plaintext)) {
            throw new \UnexpectedValueException('Invalid message');
        }

        return $plaintext;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function buildAuthenticatedData(string $key): string
    {
        return "{$this->id}.{$key}";
    }

    /**
     * Adds a plain value
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function add(string $key, string $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'data' => $this->data,
        ];
    }

    /**
     * Returns the ID for the ACL to check if the user has access to this resource
     *
     * @return string|null
     */
    public function getAclEntityId(): ?string
    {
        return (string) $this->getId() ?? null;
    }

    /**
     * @param int $id
     *
     * @return \Vcampitelli\Framework\Core\Domain\ModelInterface
     */
    public function setId(int $id): ModelInterface
    {
        $this->id = $id;
        return $this;
    }

}
