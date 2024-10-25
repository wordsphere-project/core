<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\ContentTypes;

use Closure;

class ContentTypeFieldRegistry
{
    private array $fields = [];

    public function registerFields(string $contentTypeKey, string $location, Closure $callback): void
    {
        if (! isset($this->fields[$contentTypeKey])) {
            $this->fields[$contentTypeKey] = [];
        }
        if (! isset($this->fields[$contentTypeKey][$location])) {
            $this->fields[$contentTypeKey][$location] = [];
        }
        $this->fields[$contentTypeKey][$location][] = $callback;
    }

    public function getFields(string $contentTypeKey, string $location): array
    {
        return $this->fields[$contentTypeKey][$location] ?? [];
    }

    public function getAllLocations(string $contentTypeKey): array
    {
        return array_keys($this->fields[$contentTypeKey] ?? []);
    }
}
