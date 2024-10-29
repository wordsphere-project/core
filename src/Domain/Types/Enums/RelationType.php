<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Types\Enums;

enum RelationType: string
{
    case ONE_TO_ONE = 'one_to_one';
    case ONE_TO_MANY = 'one_to_many';
    case MANY_TO_ONE = 'many_to_one';
    case MANY_TO_MANY = 'many_to_many';
    case BELONGS_TO_MANY = 'belongs_to_many';
    case BELONGS_TO = 'belongs_to';
    case HAS_MANY = 'has_many';

    public function isInverse(): bool
    {
        return in_array($this, [self::BELONGS_TO, self::BELONGS_TO_MANY]);
    }
}
