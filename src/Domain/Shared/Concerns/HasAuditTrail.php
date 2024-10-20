<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

trait HasAuditTrail
{
    use HasCreatedBy;
    use HasTimestamps;
    use HasUpdatedBy;

    protected function initializeHasAuditTrail(Uuid $creator): void
    {
        $this->initializeTimestamps();
        $this->createdBy = $creator;
        $this->updatedBy = $creator;
    }

    protected function updateAuditTrail(Uuid $updater): void
    {
        $this->updatedBy = $updater;
        $this->updateTimestamps();
    }
}
