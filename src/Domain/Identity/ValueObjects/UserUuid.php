<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Identity\ValueObjects;

use WordSphere\Core\Domain\Shared\ValueObjects\AbstractId;

class UserUuid extends AbstractId
{
    protected static function create(string $value): static
    {
        return new static($value);
    }
}
