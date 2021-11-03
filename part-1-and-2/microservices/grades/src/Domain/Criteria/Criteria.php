<?php
/**
 * @author Vinícius Campitelli <eu@viniciuscampitelli.com>
 */

declare(strict_types=1);

namespace Grades\Domain\Criteria;

use Vcampitelli\Framework\Core\Domain\ModelInterface;

/**
 * Class that represents a grading criteria
 */
class Criteria implements ModelInterface
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
     * @var int|null
     */
    private int $weight;

    /**
     * @param int|null    $id
     * @param string|null $name
     * @param int|null    $weight
     */
    public function __construct(int $id = null, string $name = null, int $weight = null)
    {
        if ($id) {
            $this->id = $id;
        }
        if ($name) {
            $this->name = $name;
        }
        if ($weight) {
            $this->weight = $weight;
        }
    }

    /**
     * @param int $id
     *
     * @return ModelInterface
     */
    public function setId(int $id): ModelInterface
    {
        $this->id = $id;
        return $this;
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
     * @return int|null
     */
    public function getWeight(): ?int
    {
        return $this->weight;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'weight' => $this->weight,
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
}
