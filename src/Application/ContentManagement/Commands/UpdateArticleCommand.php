<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Commands;

use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;

readonly class UpdateArticleCommand
{
    public function __construct(
        public ArticleId $id,
        public ?string $title = null,
        public ?string $content = null,
        public ?string $excerpt = null,
        public ?string $slug = null,
        public ?array $data = null,
    ) {}

    public function hasUpdates(): bool
    {
        return $this->title !== null
            || $this->content !== null
            || $this->excerpt !== null
            || $this->slug !== null
            || $this->data !== null;
    }
}
