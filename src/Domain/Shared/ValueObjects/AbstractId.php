<?php

namespace WordSphere\Core\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use WordSphere\Core\Domain\Shared\Concerns\HasStringTypeValue;

abstract class AbstractId
{
    use HasStringTypeValue;

    protected string $value;

    final protected function __construct(string $value)
    {
        $this->ensureValidUuid($value);
        $this->value = $value;
    }

    /**
     * Named constructor to be overridden by child classes
     */
    protected static function create(string $value): static
    {
        return new static($value);
    }

    public static function generate(): static
    {
        return static::create(Uuid::uuid4()->toString());
    }

    public function ensureValidUuid(string $uuid): void
    {
        if (! Uuid::isValid($uuid)) {
            throw new InvalidArgumentException('Invalid UUID: '.$uuid);
        }
    }
}
