<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Types\Services;

use Illuminate\Session\SessionManager;
use RuntimeException;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\Tenancy\Persistence\Models\EloquentProject;
use WordSphere\Core\Infrastructure\Tenancy\Persistence\Models\EloquentTenant;
use WordSphere\Core\Infrastructure\Types\Enums\TenancySessionKey;

readonly class TenantProjectProvider
{
    public function __construct(
        private SessionManager $session,
    ) {}

    public function getCurrentTenantId(): Uuid
    {
        $tenantId = $this->session->get(TenancySessionKey::CURRENT_TENANT_ID->value);

        if (! $tenantId) {
            $tenantId = EloquentTenant::query()->first()?->id;
            $this->session->put(TenancySessionKey::CURRENT_TENANT_ID->value, $tenantId);
        }

        if (! $tenantId) {
            throw new RuntimeException('No tenant select');
        }

        return Uuid::fromString($tenantId);

    }

    public function getCurrentProjectId(): Uuid
    {
        $projectId = $this->session->get(TenancySessionKey::CURRENT_PROJECT_ID->value);

        if (! $projectId) {
            $projectId = EloquentProject::query()->first()?->id;
            $this->session->put(TenancySessionKey::CURRENT_PROJECT_ID->value, $projectId);
        }

        if (! $projectId) {
            throw new RuntimeException('No project select');
        }

        return Uuid::fromString($projectId);
    }

    public function setCurrentTenant(Uuid $tenantId): void
    {
        $this->session->put('current_tenant_id', $tenantId->toString());
    }

    public function setCurrentProject(Uuid $projectId): void
    {
        $this->session->put('current_project_id', $projectId->toString());
    }
}
