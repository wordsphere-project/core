<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\Types\Services;

use WordSphere\Core\Application\Types\Commands\AddAllowedRelationCommand;
use WordSphere\Core\Application\Types\Commands\CreateTypeCommand;
use WordSphere\Core\Application\Types\Commands\UpdateTypeCommand;
use WordSphere\Core\Domain\Shared\Contracts\EventDispatcherInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Domain\Types\Entities\Type;
use WordSphere\Core\Domain\Types\Enums\RelationType;
use WordSphere\Core\Domain\Types\Events\AllowedRelationAdded;
use WordSphere\Core\Domain\Types\Events\TypeCreated;
use WordSphere\Core\Domain\Types\Events\TypeUpdated;
use WordSphere\Core\Domain\Types\Exceptions\TypeNotFoundException;
use WordSphere\Core\Domain\Types\Repositories\TypeRepositoryInterface;
use WordSphere\Core\Domain\Types\ValueObjects\AllowedRelation;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;

readonly class TypeService
{
    public function __construct(
        private TypeRepositoryInterface $repository,
        private TenantProjectProvider $tenantProjectProvider,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function createType(CreateTypeCommand $command): Type
    {

        $tenantId = $command->tenantId ?? $this->tenantProjectProvider->getCurrentProjectId();
        $projectId = $command->projectId ?? $this->tenantProjectProvider->getCurrentProjectId();

        $type = new Type(
            id: Uuid::generate(),
            key: $command->key,
            entityClass: $command->entityClass,
            tenantId: $tenantId,
            projectId: $projectId
        );

        $this->repository->save($type);
        $this->eventDispatcher->dispatch(new TypeCreated($type));

        return $type;

    }

    public function updateType(UpdateTypeCommand $command): Type
    {
        $tenantId = $command->tenantId ?? $this->tenantProjectProvider->getCurrentTenantId();
        $projectId = $command->projectId ?? $this->tenantProjectProvider->getCurrentProjectId();

        $type = $this->repository->findById($command->id, $tenantId, $projectId);
        if (! $type) {
            throw new TypeNotFoundException($command->id);
        }

        $updatedType = new Type(
            $type->getId(),
            $command->key,
            $command->entityClass,
            $tenantId,
            $projectId
        );

        foreach ($type->getAllowedRelations() as $relation) {
            $updatedType->addAllowedRelation($relation);
        }

        $this->repository->save($updatedType);
        $this->eventDispatcher->dispatch(new TypeUpdated($type));

        return $updatedType;

    }

    public function addAllowedRelation(AddAllowedRelationCommand $command): void
    {
        $tenantId = $this->tenantProjectProvider->getCurrentTenantId();
        $projectId = $this->tenantProjectProvider->getCurrentProjectId();

        $sourceType = $this->repository->findById($command->sourceTypeId, $tenantId, $projectId);
        if (! $sourceType) {
            throw new TypeNotFoundException($command->sourceTypeId);
        }

        $targetType = $this->repository->findById($command->targetTypeId, $tenantId, $projectId);
        if (! $targetType) {
            throw new TypeNotFoundException($command->targetTypeId);
        }

        $relation = new AllowedRelation(
            $command->name,
            $sourceType,
            $targetType,
            RelationType::from($command->relationType),
            $command->isRequired,
            $command->minItems,
            $command->maxItems,
            $command->inverseRelationName
        );

        $sourceType->addAllowedRelation($relation);
        $this->repository->save($sourceType);

        $this->eventDispatcher->dispatch(new AllowedRelationAdded($relation));

    }
}
