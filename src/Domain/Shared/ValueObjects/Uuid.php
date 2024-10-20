<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\ValueObjects;

class Uuid extends AbstractId
{
    protected static function create(string $value): static
    {
        return new static($value);
    }
}
