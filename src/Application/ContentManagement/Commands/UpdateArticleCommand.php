<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Commands;

use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;

final class UpdateArticleCommand
{
    private array $updatedFields = [];

    public function __construct(
        public ArticleId $id,
        public ?string $title = null,
        public ?string $content = null,
        public ?string $excerpt = null,
        public ?string $slug = null,
        public ?array $data = null,
    ) {
        $this->updatedFields = array_keys(array_filter(get_object_vars($this), function ($value, $key) {
            return $key !== 'id' && $key !== 'updatedFields';
        }, ARRAY_FILTER_USE_BOTH));
    }

    public function getUpdatedFields(): array
    {
        return $this->updatedFields;
    }
}
