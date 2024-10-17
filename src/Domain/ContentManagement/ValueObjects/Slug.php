<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\ValueObjects;

use InvalidArgumentException;

use function strtolower;

class Slug
{
    private string $value;

    private function __construct(string $value)
    {
        $this->setValue($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    private function setValue(string $value): void
    {
        $slug = $this->generateSlug($value);
        if (empty($slug)) {
            throw new InvalidArgumentException('Slug cannot be empty');
        }

        $this->value = $slug;
    }

    private function generateSlug(string $value): string
    {
        $slug = strtolower($value);

        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        return trim($slug, '-');

    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function incrementSlug(): self
    {
        $parts = explode('-', $this->value);
        $lastPart = end($parts);

        if (ctype_digit($lastPart)) {
            $number = intval($lastPart);
            $parts[count($parts) - 1] = $number + 1;
        } else {
            $parts[] = '1';
        }

        return new self(implode('-', $parts));
    }
}
