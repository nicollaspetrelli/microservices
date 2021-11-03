<?php
/**
 * @author Vinícius Campitelli <eu@viniciuscampitelli.com>
 */

declare(strict_types=1);

namespace Grades\Domain\Grade;

use Vcampitelli\Framework\Core\Domain\ModelInterface;

/**
 * Class that represents a grading criteria
 */
class Grade implements ModelInterface
{
    /**
     * @var int|null
     */
    private ?int $id = null;

    /**
     * @var int|null
     */
    private ?int $idApplication = null;

    /**
     * @var int|null
     */
    private ?int $idCriteria = null;

    /**
     * @var float|null
     */
    private ?float $grade = null;

    /**
     * @param int|null   $id
     * @param int|null   $idApplication
     * @param int|null   $idCriteria
     * @param float|null $grade
     */
    public function __construct(
        int $id = null,
        int $idApplication = null,
        int $idCriteria = null,
        float $grade = null
    ) {
        if ($id > 0) {
            $this->id = $id;
        }
        if ($idApplication > 0) {
            $this->idApplication = $idApplication;
        }
        if ($idCriteria > 0) {
            $this->idCriteria = $idCriteria;
        }
        if ($grade) {
            $this->setGrade($grade);
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
     * @return int|null
     */
    public function getIdApplication(): ?int
    {
        return $this->idApplication;
    }

    /**
     * @return int|null
     */
    public function getIdCriteria(): ?int
    {
        return $this->idCriteria;
    }

    /**
     * @return float|null
     */
    public function getGrade(): ?float
    {
        return $this->grade;
    }

    /**
     * @param float $grade
     *
     * @return $this
     */
    public function setGrade(float $grade): self
    {
        $this->grade = \max($grade, 0);
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id'             => $this->id,
            'id_application' => $this->idApplication,
            'id_criteria'    => $this->idCriteria,
            'grade'          => $this->grade,
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
