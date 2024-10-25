<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Commands;

use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

final class UpdateContentCommand
{
    private array $updatedFields = [];

    public function __construct(
        public Uuid $id,
        public string $type,
        public Uuid $updatedBy,
        public ?string $title = null,
        public ?string $content = null,
        public ?string $excerpt = null,
        public ?string $slug = null,
        public ?array $customFields = null,
        public ?Id $featuredImage = null,
        public ?array $media = []
    ) {
        foreach (['title', 'content', 'excerpt', 'slug', 'customFields', 'featuredImage', 'media'] as $field) {
            if ($this->$field !== null) {
                $this->updatedFields[] = $field;
            }
        }
    }

    public function getUpdatedFields(): array
    {
        return $this->updatedFields;
    }
}
