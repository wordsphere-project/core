<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\ValueObjects;

class ArticleId
{
    private readonly int $value;

    final public function __construct(int $value)
    {
        $this->value = $value;
    }

    protected static function create(int $value): static
    {
        return new static($value);
    }

    public static function fromInt(int $value): static
    {
        return static::create($value);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function toInt(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function __toInt(): int
    {
        return $this->value;
    }
}
