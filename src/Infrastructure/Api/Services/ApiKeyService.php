<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Api\Services;

use Illuminate\Support\Facades\Cache;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\Api\Persistence\Models\ApiKeyModel;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;

class ApiKeyService
{
    public function __construct(
        private readonly TenantProjectProvider $tenantProjectProvider
    ) {}

    public function isValidKey(string $key): bool
    {
        return Cache::remember("api_key:$key", 3600, function () use ($key) {
            return ApiKeyModel::query()->where('key', $key)
                ->where('active', true)
                ->exists();
        });
    }

    public function setCurrentTenant(string $key): void
    {
        $apiKey = ApiKeyModel::query()->where('key', $key)->first();
        if ($apiKey) {
            $this->tenantProjectProvider->setCurrentTenant(Uuid::fromString($apiKey->tenant_id));
            $this->tenantProjectProvider->setCurrentProject(Uuid::fromString($apiKey->project_id));
        }
    }
}
