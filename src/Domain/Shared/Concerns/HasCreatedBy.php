<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use WordSphere\Core\Domain\Identity\ValueObjects\UserId;

trait HasCreatedBy
{
    private UserId $createdBy;

    public function getCreatedBy(): UserId
    {
        return $this->createdBy;
    }

    protected function setCreatedBy(UserId $createdBy): void
    {
        if (! isset($this->createdBy)) {
            $this->createdBy = $createdBy;
        }
    }
}
