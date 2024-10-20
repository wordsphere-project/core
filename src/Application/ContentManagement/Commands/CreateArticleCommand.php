<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Commands;

use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;
use WordSphere\Core\Domain\MediaManagement\ValueObjects\MediaId;

readonly class CreateArticleCommand
{
    public function __construct(
        public UserUuid $creator,
        public string $title,
        public ?string $content = null,
        public ?string $excerpt = null,
        public ?string $slug = null,
        public ?array $customFields = [],
        public ?MediaId $featuredImage = null
    ) {}
}
