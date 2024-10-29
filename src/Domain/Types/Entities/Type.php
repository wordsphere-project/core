<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Types\Entities;

use InvalidArgumentException;
use WordSphere\Core\Domain\Shared\Concerns\HasTenantAndProject;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Domain\Types\Contracts\TypeableInterface;
use WordSphere\Core\Domain\Types\ValueObjects\AllowedRelation;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;

class Type
{
    use HasTenantAndProject;

    private array $allowedRelations = [];

    private array $customFields = [];

    private array $interfaceData = [];

    private ?Uuid $parentId = null;

    public function __construct(
        private readonly Uuid $id,
        private readonly TypeKey $key,
        private readonly string $entityClass,
        Uuid $tenantId,
        Uuid $projectId,
        ?Uuid $parentId = null,
    ) {
        $this->tenantId = $tenantId;
        $this->projectId = $projectId;
        $this->parentId = $parentId;
        $this->validateEntityClass($entityClass);
    }

    private function validateEntityClass(string $entityClass): void
    {
        if (! class_exists($entityClass)) {
            throw new InvalidArgumentException("Entity class {$entityClass} does not exist");
        }

        $interfaces = class_implements($entityClass);
        if (! isset($interfaces[TypeableInterface::class])) {
            throw new InvalidArgumentException('Entity class must implement TypeableInterface');
        }

        if ($this->tenantId->equals($this->projectId)) {
            throw new InvalidArgumentException("Entity class {$entityClass} must have a tenant id");
        }
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getKey(): TypeKey
    {
        return $this->key;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getParentId(): ?Uuid
    {
        return $this->parentId;
    }

    public function addInterfaceData(array $data): void
    {
        $this->interfaceData = array_merge($this->interfaceData, $data);
    }

    public function getInterfaceData(): array
    {
        return $this->interfaceData;
    }

    public function addCustomFieldsData(array $data): void
    {
        $this->customFields = array_merge($this->customFields, $data);
    }

    public function getCustomFields(): array
    {
        return $this->customFields;
    }

    public function addAllowedRelation(AllowedRelation $relation): void
    {
        $this->allowedRelations[$relation->getName()] = $relation;
    }

    /**
     * @return array<int, AllowedRelation>
     */
    public function getAllowedRelations(): array
    {
        return $this->allowedRelations;
    }

    public function canRelateWith(Type $targetType, string $relationName): bool
    {
        if (! isset($this->allowedRelations[$relationName])) {
            return false;
        }

        $allowedRelation = $this->allowedRelations[$relationName];

        return $allowedRelation->getTargetType()->getKey()->equals($targetType->getKey());
    }
}
