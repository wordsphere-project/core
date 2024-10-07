<?php

declare(strict_types=1);

namespace WordSphere\Core\Enums;

use Filament\Support\Contracts\HasLabel;

use function __;

enum ContentStatus: int implements HasLabel
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
            self::DRAFT => __('status.draft'),
            self::IN_REVIEW => __('status.in_review'),
            self::APPROVED => __('status.approved'),
            self::PUBLISHED => __('status.published'),
            self::SCHEDULED => __('status.scheduled'),
            self::ARCHIVED => __('status.archived'),
            self::DELETED => __('status.deleted'),
            self::NEEDS_REVISION => __('status.needs_revision'),
            self::UNPUBLISHED => __('status.unpublished'),
        };
    }
}
