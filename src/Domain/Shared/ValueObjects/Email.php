<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\ValueObjects;

use const FILTER_VALIDATE_EMAIL;

use InvalidArgumentException;
use WordSphere\Core\Domain\Shared\Concerns\HasStringTypeValue;

use function filter_var;

class Email
{
    use HasStringTypeValue;

    private string $value;

    final protected function __construct(string $value)
    {
        $this->ensureValidEmail($value);
        $this->value = $value;
    }

    protected static function create(string $value): static
    {
        return new static($value);
    }

    private function ensureValidEmail(string $value): void
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }
    }
}
