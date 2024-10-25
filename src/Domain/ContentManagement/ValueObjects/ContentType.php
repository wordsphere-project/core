<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\ValueObjects;

readonly class ContentType
{
    public function __construct(
        public string $key,
        public string $singularName,
        public string $pluralName,
        public string $navigationGroup,
        public string $description,
        public string $icon
    ) {}

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'singularName' => $this->singularName,
            'pluralName' => $this->pluralName,
            'navigationGroup' => $this->navigationGroup,
            'description' => $this->description,
            'icon' => $this->icon,
        ];
    }

    public function toString(): string
    {
        return $this->key;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
