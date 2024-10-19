<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use DateTimeImmutable;

trait HasTimestamps
{
    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $lastUpdatedAt;

    private function initializeTimestamps(): void
    {
        $this->createdAt = new DateTimeImmutable;
        $this->lastUpdatedAt = new DateTimeImmutable;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLastUpdatedAt(): DateTimeImmutable
    {
        return $this->lastUpdatedAt;
    }

    public function updateTimestamps(): void
    {
        $this->lastUpdatedAt = new DateTimeImmutable;
    }
}
