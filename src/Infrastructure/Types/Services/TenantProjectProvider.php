<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Types\Services;

use Illuminate\Session\SessionManager;
use RuntimeException;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\Types\Enums\TenancySessionKey;

class TenantProjectProvider
{
    public function __construct(
        private SessionManager $session,
    ) {}

    public function getCurrentTenantId(): Uuid
    {
        $tenantId = $this->session->get(TenancySessionKey::CURRENT_TENANT_ID->value);
        if (! $tenantId) {
            throw new RuntimeException('No tenant select');
        }

        return Uuid::fromString($tenantId);

    }

    public function getCurrentProjectId(): Uuid
    {
        $tenantId = $this->session->get(TenancySessionKey::CURRENT_PROJECT_ID->value);
        if (! $tenantId) {
            throw new RuntimeException('No tenant select');
        }

        return Uuid::fromString($tenantId);
    }
}
