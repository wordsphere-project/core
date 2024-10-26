<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Types\Queries;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Domain\Types\Entities\Type;
use WordSphere\Core\Domain\Types\TypeRepositoryInterface;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;

readonly class TypeQueries
{
    public function __construct(
        private TypeRepositoryInterface $repository,
        private TenantProjectProvider $tenantProjectProvider
    ) {}

    public function findTypeById(Uuid $id): ?Type
    {
        return $this->repository->findById(
            id: $id,
            tenantId: $this->tenantProjectProvider->getCurrentTenantId(),
            projectId: $this->tenantProjectProvider->getCurrentProjectId(),
        );
    }

    public function findTypeByKey(TypeKey $key): ?Type
    {
        return $this->repository->findByKey(
            $key,
            $this->tenantProjectProvider->getCurrentTenantId(),
            $this->tenantProjectProvider->getCurrentProjectId()
        );
    }

    public function getTypesForCurrentContext(): array
    {
        // Implementation will depend on your specific needs
        // This is just a placeholder
        return [];
    }
}
