<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\ValueObjects;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

final class ArticleId
{
    private string $value;

    private function __construct(string $value)
    {
        $this->ensureIsValidUuid($value);
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $id): self
    {
        return new self(value: $id);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function ensureIsValidUuid(string $id): void
    {
        if (! Uuid::isValid($id)) {
            throw new InvalidArgumentException("Invalid UUID: $id");
        }
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
