<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Commands;

readonly class CreateArticleCommand
{
    public function __construct(
        public string $title,
        public ?string $content = null,
        public ?string $excerpt = null,
        public ?string $slug = null,
        public ?array $data = [],
    ) {}
}
