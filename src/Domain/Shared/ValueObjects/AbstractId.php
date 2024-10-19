<?php

namespace WordSphere\Core\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

abstract class AbstractId
{
    protected string $value;

    final protected function __construct(string $value)
    {
        $this->ensureValidUuid($value);
        $this->value = $value;
    }

    public static function generate(): static
    {
        return static::create(Uuid::uuid4()->toString());
    }

    public static function fromString(string $value): static
    {
        return static::create($value);
    }

    /**
     * Named constructor to be overridden by child classes
     */
    protected static function create(string $value): static
    {
        return new static($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value && get_class($this) === get_class($other);
    }

    public function ensureValidUuid(string $uuid): void
    {
        if (! Uuid::isValid($uuid)) {
            throw new InvalidArgumentException('Invalid UUID: '.$uuid);
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
