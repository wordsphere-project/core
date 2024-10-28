<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\ContentTypes;

use Closure;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;

class ContentTypeFieldRegistry
{
    private array $fields = [];

    public function registerFields(TypeKey $contentTypeKey, string $location, Closure $callback): void
    {
        if (! isset($this->fields[$contentTypeKey->toString()])) {
            $this->fields[$contentTypeKey->toString()] = [];
        }
        if (! isset($this->fields[$contentTypeKey->toString()][$location])) {
            $this->fields[$contentTypeKey->toString()][$location] = [];
        }
        $this->fields[$contentTypeKey->toString()][$location][] = $callback;
    }

    public function getFields(TypeKey $contentTypeKey, string $location): array
    {
        return $this->fields[$contentTypeKey->toString()][$location] ?? [];
    }

    public function getAllLocations(TypeKey $contentTypeKey): array
    {
        return array_keys($this->fields[$contentTypeKey->toString()] ?? []);
    }
}
