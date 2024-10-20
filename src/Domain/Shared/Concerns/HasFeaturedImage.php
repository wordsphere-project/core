<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;
use WordSphere\Core\Domain\MediaManagement\ValueObjects\Id;

use function method_exists;

trait HasFeaturedImage
{
    private ?Id $featuredImageId = null;

    public function getFeaturedImage(): ?Id
    {
        return $this->featuredImageId;
    }

    public function updateFeaturedImage(?Id $featuredImageId, ?UserUuid $updater = null): void
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
