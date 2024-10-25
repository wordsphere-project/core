<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Facades;

use WordSphere\Core\Domain\ContentManagement\ContentTypeRegistry;

class ContentTypes
{
    protected static function getFacadeAccessor(): string
    {
        return ContentTypeRegistry::class;
    }
}
