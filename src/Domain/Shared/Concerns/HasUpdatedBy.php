<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;

trait HasUpdatedBy
{
    private UserUuid $updatedBy;

    public function getUpdatedBy(): UserUuid
    {
        return $this->updatedBy;
    }

    protected function setUpdatedBy(UserUuid $updatedBy): void
    {
        if (! isset($this->$updatedBy)) {
            $this->updatedBy = $updatedBy;
        }
    }
}
