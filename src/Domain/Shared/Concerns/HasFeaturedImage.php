<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\Identity\ValueObjects\UserId;
use WordSphere\Core\Domain\MediaManagement\ValueObjects\MediaId;

use function method_exists;

trait HasFeaturedImage
{
    private ?MediaId $featuredImageId = null;

    public function getFeaturedImage(): ?MediaId
    {
        return $this->featuredImageId;
    }

    public function updateFeaturedImage(?MediaId $featuredImageId, ?UserId $updater = null): void
    {
        $this->featuredImageId = $featuredImageId;

        if (method_exists($this, 'updateAuditTrail') && $updater !== null) {
            $this->updateAuditTrail($updater);

            return;
        }

        if (method_exists($this, 'updateTimestamps')) {
            $this->updateTimestamps();
        }

    }
}
