<?php

namespace WordSphere\Core\Application\ContentManagement\Commands;

use WordSphere\Core\Domain\ContentManagement\Enums\ContentStatus;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

readonly class ChangeContentStatusCommand
{
    public function __construct(
        public string $id,
        public ContentStatus $newStatus,
        public Uuid $statusChangedBy
    ) {}
}
