<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

trait HasUpdatedBy
{
    private Uuid $updatedBy;

    public function getUpdatedBy(): Uuid
    {
        return $this->updatedBy;
    }

    protected function setUpdatedBy(Uuid $updatedBy): void
    {
        if (! isset($this->$updatedBy)) {
            $this->updatedBy = $updatedBy;
        }
    }
}
