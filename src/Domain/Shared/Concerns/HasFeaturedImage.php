<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\MediaManagement\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

use function method_exists;

trait HasFeaturedImage
{
    private ?Id $featuredImageId = null;

    public function getFeaturedImage(): ?Id
    {
        return $this->featuredImageId;
    }

    public function updateFeaturedImage(?Id $featuredImageId, ?Uuid $updater = null): void
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
