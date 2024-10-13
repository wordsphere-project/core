<?php

declare(strict_types=1);

namespace WordSphere\Core\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

use function __;

enum ContentStatus: int implements HasColor, HasLabel
{
    case DRAFT = 0;
    case IN_REVIEW = 1;
    case APPROVED = 2;
    case PUBLISHED = 3;
    case SCHEDULED = 4;
    case ARCHIVED = 5;
    case DELETED = 6;
    case NEEDS_REVISION = 7;
    case UNPUBLISHED = 8;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => __('wordsphere::status.draft'),
            self::IN_REVIEW => __('wordsphere::status.in_review'),
            self::APPROVED => __('wordsphere::status.approved'),
            self::PUBLISHED => __('wordsphere::status.published'),
            self::SCHEDULED => __('wordsphere::status.scheduled'),
            self::ARCHIVED => __('wordsphere::status.archived'),
            self::DELETED => __('wordsphere::status.deleted'),
            self::NEEDS_REVISION => __('wordsphere::status.needs_revision'),
            self::UNPUBLISHED => __('wordsphere::status.unpublished'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::IN_REVIEW => 'gray',
            self::APPROVED => 'warning',
            self::PUBLISHED => 'success',
            self::SCHEDULED => 'gray',
            self::ARCHIVED => 'danger',
            self::DELETED => 'danger',
            self::NEEDS_REVISION => 'danger',
            self::UNPUBLISHED => 'danger',
        };
    }
}
