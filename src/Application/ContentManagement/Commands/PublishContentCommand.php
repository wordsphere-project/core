<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Commands;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

readonly class PublishContentCommand
{
    public function __construct(
        public string $id,
        public Uuid $publishedBy
    ) {}
}
