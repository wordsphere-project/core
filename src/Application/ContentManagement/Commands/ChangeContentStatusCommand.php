<?php

namespace WordSphere\Core\Application\ContentManagement\Commands;

use WordSphere\Core\Domain\ContentManagement\Enums\ArticleStatus;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

readonly class ChangeContentStatusCommand
{
    public function __construct(
        public string $id,
        public ArticleStatus $newStatus,
        public Uuid $statusChangedBy
    ) {}
}
