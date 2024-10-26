<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Types\ValueObjects;

use InvalidArgumentException;
use WordSphere\Core\Domain\Types\Entities\Type;
use WordSphere\Core\Domain\Types\Enums\RelationType;

class AllowedRelation
{
    public function __construct(
        private string $name,
        private Type $sourceType,
        private Type $targetType,
        private RelationType $relationType,
        private bool $isRequired = false,
        private ?int $minItems = null,
        private ?int $maxItems = null,
        private ?string $inverseRelationName = null,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->relationType->isInverse() && $this->inverseRelationName === null) {
            throw new InvalidArgumentException('Inverse relation name is required for belongsTo relationships');
        }

        if ($this->minItems !== null && $this->maxItems !== null && $this->minItems > $this->maxItems) {
            throw new InvalidArgumentException('Min items cannot be greater than max items');
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSourceType(): Type
    {
        return $this->sourceType;
    }

    public function getTargetType(): Type
    {
        return $this->targetType;
    }

    public function getRelationType(): RelationType
    {
        return $this->relationType;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function getMinItems(): ?int
    {
        return $this->minItems;
    }

    public function getMaxItems(): ?int
    {
        return $this->maxItems;
    }

    public function getInverseRelationName(): ?string
    {
        return $this->inverseRelationName;
    }
}
