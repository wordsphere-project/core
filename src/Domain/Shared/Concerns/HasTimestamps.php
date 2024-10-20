<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Concerns;

use DateTimeImmutable;

trait HasTimestamps
{
    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $updatedAt;

    private function initializeTimestamps(): void
    {
        $this->createdAt = new DateTimeImmutable;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateTimestamps(): void
    {
        $this->updatedAt = new DateTimeImmutable;
    }
}
