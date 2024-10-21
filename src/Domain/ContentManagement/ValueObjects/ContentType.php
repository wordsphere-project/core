<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\ValueObjects;

class ContentType
{

    public function __construct (
        public readonly string $key,
        public readonly string $singularName,
        public readonly string $pluralName,
        public readonly string $description,
        public readonly string $icon
    ) { }

}
