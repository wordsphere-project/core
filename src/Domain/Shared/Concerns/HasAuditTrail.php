<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

trait HasAuditTrail
{
    use HasCreatedBy;
    use HasTimestamps;
    use HasUpdatedBy;

    protected function initializeHasAuditTrail(?Uuid $creator = null): void
    {
        $this->initializeTimestamps();

        if ($creator !== null) {
            $this->createdBy = $creator;
            $this->updatedBy = $creator;
        }

    }

    protected function updateAuditTrail(?Uuid $updater = null): void
    {
        if ($updater !== null and property_exists($this, 'updatedBy')) {
            $this->updatedBy = $updater;
        }
        $this->updateTimestamps();
    }

    public function setCreatedBy(Uuid $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function setUpdatedBy(Uuid $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }
}
