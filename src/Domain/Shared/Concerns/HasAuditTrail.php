<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use DateTimeImmutable;
use WordSphere\Core\Domain\Identity\ValueObjects\UserId;

trait HasAuditTrail
{
    use HasCreatedBy;
    use HasLastUpdatedBy;
    use HasTimestamps;

    protected function initializeHasAuditTrail(UserId $creator): void
    {
        $now = new DateTimeImmutable;
        $this->initializeTimestamps();
        $this->createdBy = $creator;
        $this->lastUpdatedBy = $creator;
        $this->lastUpdatedAt = $now;
    }

    protected function updateAuditTrail(UserId $updater): void
    {
        $this->lastUpdatedBy = $updater;
        $this->lastUpdatedAt = new DateTimeImmutable;
    }
}
