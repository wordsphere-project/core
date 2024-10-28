<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Contracts;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

interface TenantAware
{
    public function getTenantId(): Uuid;
}
