<?php

declare(strict_types=1);

namespace Framework\Core\Domain;

abstract class AbstractTransactionalModel implements ModelInterface, \JsonSerializable
{

    /**
     * ID for a Pending state
     *
     * @var int
     */
    const STATE_PENDING = 1;

    /**
     * ID for a Completed state
     *
     * @var int
     */
    const STATE_COMPLETED = 2;

    /**
     * ID for a Denied state
     *
     * @var int
     */
    const STATE_DENIED = 3;

    /**
     * @var string
     */
    private const METADATA_ARRAY_INDEX = '__metadata__';

    /**
     * @var string
     */
    private const STATE_ARRAY_INDEX = 'state';

    /**
     * @var string
     */
    private const STATE_REASON_ARRAY_INDEX = 'state_reason';

    /**
     * @var int
     */
    private int $state = self::STATE_PENDING;

    /**
     * @var string|null
     */
    private ?string $stateReason;

    /**
     * Sets current state as Pending (default)
     *
     * @param string|null $reason
     *
     * @return $this
     */
    public function setPendingState(string $reason = null): self
    {
        $this->state = self::STATE_PENDING;
        $this->stateReason = $reason;

        return $this;
    }

    /**
     * Returns if the current state is Pending
     *
     * @return bool
     */
    public function isPendingState(): bool
    {
        return $this->state === self::STATE_PENDING;
    }

    /**
     * Sets current state as Completed
     *
     * @param string|null $reason
     *
     * @return $this
     */
    public function setCompletedState(string $reason = null): self
    {
        $this->state = self::STATE_COMPLETED;
        $this->stateReason = $reason;

        return $this;
    }

    /**
     * Returns if the current state is Completed
     *
     * @return bool
     */
    public function isCompletedState(): bool
    {
        return $this->state === self::STATE_COMPLETED;
    }

    /**
     * Sets current state as Denied
     *
     * @param string|null $reason
     *
     * @return $this
     */
    public function setDeniedState(string $reason = null): self
    {
        $this->state = self::STATE_DENIED;
        $this->stateReason = $reason;

        return $this;
    }

    /**
     * Returns if the current state is Denied
     *
     * @return bool
     */
    public function isDeniedState(): bool
    {
        return $this->state === self::STATE_DENIED;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            self::METADATA_ARRAY_INDEX => [
                self::STATE_ARRAY_INDEX => $this->state,
            ],
        ] + $this->toArrayFromProxy();
        if (!empty($this->stateReason)) {
            $data[self::METADATA_ARRAY_INDEX][self::STATE_REASON_ARRAY_INDEX] = $this->stateReason;
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return ModelInterface
     */
    public static function build(array $data): ModelInterface
    {
        $model = static::buildFromProxy($data);
        if (isset($data[self::METADATA_ARRAY_INDEX])) {
            if (!\is_array($data[self::METADATA_ARRAY_INDEX])) {
                $data[self::METADATA_ARRAY_INDEX] = (array)$data[self::METADATA_ARRAY_INDEX];
            }
            if (isset($data[self::METADATA_ARRAY_INDEX][self::STATE_ARRAY_INDEX])) {
                switch ($data[self::METADATA_ARRAY_INDEX][self::STATE_ARRAY_INDEX]) {
                    case self::STATE_PENDING:
                    case self::STATE_COMPLETED:
                    case self::STATE_DENIED:
                        $model->state = $data[self::METADATA_ARRAY_INDEX][self::STATE_ARRAY_INDEX];
                        break;
                }
            }
            if (isset($data[self::METADATA_ARRAY_INDEX][self::STATE_REASON_ARRAY_INDEX])) {
                $model->stateReason = $data[self::METADATA_ARRAY_INDEX][self::STATE_REASON_ARRAY_INDEX];
            }
        }

        return $model;
    }

    /**
     * @param array $data
     *
     * @return ModelInterface
     */
    protected static function buildFromProxy(array $data): ModelInterface
    {
        return new static();
    }

    /**
     * @return array|int[]
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Inner call that will be wrapped by transactional values
     *
     * @return array
     */
    abstract protected function toArrayFromProxy(): array;

}
