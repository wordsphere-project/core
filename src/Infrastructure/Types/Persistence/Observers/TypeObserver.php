<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Types\Persistence\Observers;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\Types\Persistence\Cache\CachedTypeRepository;
use WordSphere\Core\Infrastructure\Types\Persistence\Models\AllowedRelationModel;
use WordSphere\Core\Infrastructure\Types\Persistence\Models\TypeModel;

class TypeObserver
{
    public function __construct(
        private CachedTypeRepository $cache
    ) {}

    public function created(TypeModel $type): void
    {
        $this->clearCache($type);
    }

    public function updated(TypeModel $type): void
    {
        $this->clearCache($type);
    }

    public function deleted(TypeModel $type): void
    {
        $this->clearCache($type);

        // Clean up related allowed relations
        AllowedRelationModel::query()->where('source_type_id', $type->id)
            ->orWhere('target_type_id', $type->id)
            ->delete();
    }

    private function clearCache(TypeModel $type): void
    {
        $this->cache->clearAllTypesCache(
            Uuid::fromString($type->tenant_id),
            Uuid::fromString($type->project_id)
        );
    }
}
