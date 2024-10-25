<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Commands;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

readonly class CreateContentCommand
{
    public function __construct(
        public string $type,
        public Uuid $createdBy,
        public string $title,
        public ?string $content = null,
        public ?string $excerpt = null,
        public ?string $slug = null,
        public ?array $customFields = [],
        public ?int $featuredImage = null,
        public ?array $media = []
    ) {}
}
