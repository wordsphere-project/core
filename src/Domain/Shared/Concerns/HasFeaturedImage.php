<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\ContentManagement\ValueObjects\Media;

use function method_exists;

trait HasFeaturedImage
{
    private ?Media $featuredImage = null;

    public function getFeaturedImage(): ?Media
    {
        return $this->featuredImage;
    }

    public function updateFeaturedImage(?Media $featuredImage): void
    {
        $this->featuredImage = $featuredImage;

        if (method_exists($this, 'updateAuditTrail')) {
            $this->updateAuditTrail();

            return;
        }

        if (method_exists($this, 'updateTimestamps')) {
            $this->updateTimestamps();
        }

    }
}
