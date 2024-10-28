<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

trait HasTenantAndProject
{
    private Uuid $tenantId;

    private Uuid $projectId;

    public function getTenantId(): Uuid
    {
        return $this->tenantId;
    }

    public function getProjectId(): Uuid
    {
        return $this->projectId;
    }
}
