<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

use function method_exists;

trait HasFeaturedImage
{
    private ?Id $featuredImageId = null;

    private ?string $featuredImageUrl = null;

    public function getFeaturedImageUrl(): ?string
    {
        return $this->featuredImageUrl;
    }

    public function getFeaturedImageId(): ?Id
    {
        return $this->featuredImageId;
    }

    public function updateFeaturedImageUrl(?string $featuredImageUrl): void
    {
        $this->featuredImageUrl = $featuredImageUrl;

        if (method_exists($this, 'updateAuditTrail')) {
            $this->updateAuditTrail();

            return;
        }

        if (method_exists($this, 'updateTimestamps')) {
            $this->updateTimestamps();
        }

    }

    public function updateFeaturedImageId(?Id $featuredImageId, ?Uuid $updater = null): void
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
