<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;

trait HasCreatedBy
{
    private UserUuid $createdBy;

    public function getCreatedBy(): UserUuid
    {
        return $this->createdBy;
    }

    protected function setCreatedBy(UserUuid $createdBy): void
    {
        if (! isset($this->createdBy)) {
            $this->createdBy = $createdBy;
        }
    }
}
