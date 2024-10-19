<?php

namespace WordSphere\Core\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

abstract class AbstractId
{

    protected string $value;

    protected function __construct(string $value)
    {
        $this->ensureValidUuid($value);
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new static(Uuid::uuid4()->toString());
    }

    public static function fromString(string $value): self
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
        if (!Uuid::isValid($uuid)) {
            throw new InvalidArgumentException('Invalid UUID: ' . $uuid);
        }
    }

}
