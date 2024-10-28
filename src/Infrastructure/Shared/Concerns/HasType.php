<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Shared\Concerns;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Domain\Types\TypeRegistry;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;

use function app;

/**
 * @property string $type
 */
trait HasType
{
    public function getTypeId(): Uuid
    {
        $type = app(TypeRegistry::class)->get(TypeKey::fromString($this->getTypeKey()));

        return $type->getId();
    }

    public function getTypeKey(): string
    {

        return TypeKey::fromString($this->type)->toString();
    }
}
