<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\ValueObjects;

readonly class ContentType
{
    public function __construct(
        public string $key,
        public string $singularName,
        public string $pluralName,
        public string $description,
        public string $icon
    ) {}
}
