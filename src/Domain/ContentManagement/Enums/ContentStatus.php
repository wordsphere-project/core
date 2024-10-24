<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Enums;

enum ContentStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';

    public function isDraft(): bool
    {
        return $this === self::DRAFT;
    }

    public function isPublished(): bool
    {
        return $this === self::PUBLISHED;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
