<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Types\ValueObjects;

use InvalidArgumentException;

final class TypeKey
{
    private const PATTERN = '/^[a-z0-9_-]+$/';

    private function __construct(
        private string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    private function validate(string $value): void
    {
        if (! preg_match(self::PATTERN, $value)) {
            throw new InvalidArgumentException(
                'Type key must contain only lowercase letters, numbers, underscores, and hyphens'
            );
        }
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
