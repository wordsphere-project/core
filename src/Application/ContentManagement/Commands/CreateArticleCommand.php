<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Commands;

use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;
use WordSphere\Core\Domain\MediaManagement\ValueObjects\Id;

readonly class CreateArticleCommand
{
    public function __construct(
        public UserUuid $createdBy,
        public string $title,
        public ?string $content = null,
        public ?string $excerpt = null,
        public ?string $slug = null,
        public ?array $customFields = [],
        public ?Id $featuredImage = null
    ) {}
}
