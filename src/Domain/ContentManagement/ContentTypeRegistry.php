<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement;

use WordSphere\Core\Domain\ContentManagement\ValueObjects\ContentType;

class ContentTypeRegistry
{
    private array $contentTypes = [];

    public function register(ContentType $contentType): void
    {
        $this->contentTypes[$contentType->key] = $contentType;
    }

    public function get(string $key): ?ContentType
    {
        return $this->contentTypes[$key] ?? null;
    }

    public function all(): array
    {
        return $this->contentTypes;
    }
}
