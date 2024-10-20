<?php

namespace WordSphere\Core\Domain\Shared\Concerns;

use function get_class;

trait HasStringTypeValue
{
    public static function fromString(string $value): static
    {
        return static::create($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value && get_class($this) === get_class($other);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
