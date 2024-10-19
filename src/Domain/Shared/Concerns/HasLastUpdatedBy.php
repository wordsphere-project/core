<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\Identity\ValueObjects\UserId;

trait HasLastUpdatedBy
{
    private UserId $lastUpdatedBy;

    public function getLastUpdatedBy(): UserId
    {
        return $this->lastUpdatedBy;
    }

    protected function setLastUpdatedBy(UserId $lastUpdatedBy): void
    {
        if (! isset($this->$lastUpdatedBy)) {
            $this->lastUpdatedBy = $lastUpdatedBy;
        }
    }
}
