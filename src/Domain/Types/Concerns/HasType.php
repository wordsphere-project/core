<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Types\Concerns;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

trait HasType
{
    private Uuid $typeId;

    private string $typeKey;

    private string $typeEntityClassName;

    public function getTypeId(): Uuid
    {
        return $this->typeId;
    }

    public function getTypeKey(): string
    {
        return $this->typeKey;
    }

    public function getTypeEntityClass(): string
    {
        return $this->typeEntityClassName;
    }
}
