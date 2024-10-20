<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;

trait HasAuditTrail
{
    use HasCreatedBy;
    use HasTimestamps;
    use HasUpdatedBy;

    protected function initializeHasAuditTrail(UserUuid $creator): void
    {
        $this->initializeTimestamps();
        $this->createdBy = $creator;
        $this->updatedBy = $creator;
    }

    protected function updateAuditTrail(UserUuid $updater): void
    {
        $this->updatedBy = $updater;
        $this->updateTimestamps();
    }
}
