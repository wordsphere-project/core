<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\MediaManagement\ValueObjects;

use WordSphere\Core\Domain\Shared\ValueObjects\AbstractId;

class MediaId extends AbstractId
{
    protected static function create(string $value): static
    {
        return new static($value);
    }
}
