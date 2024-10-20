<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

trait HasCreatedBy
{
    private Uuid $createdBy;

    public function getCreatedBy(): Uuid
    {
        return $this->createdBy;
    }

    protected function setCreatedBy(Uuid $createdBy): void
    {
        if (! isset($this->createdBy)) {
            $this->createdBy = $createdBy;
        }
    }
}
