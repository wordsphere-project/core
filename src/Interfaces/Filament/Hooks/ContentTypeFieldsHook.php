<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Hooks;

use WordSphere\Core\Domain\ContentManagement\ContentTypeRegistry;
use WordSphere\Core\Interfaces\Filament\ContentTypes\ContentTypeFieldRegistry;

readonly class ContentTypeFieldsHook
{
    public function __construct(
        private ContentTypeFieldRegistry $fieldRegistry,
        private ContentTypeRegistry $contentTypeRegistry,
    ) {}

    public function getCustomFields(string $contentTypeKey, string $location): array
    {
        $contentType = $this->contentTypeRegistry->get($contentTypeKey);
        if (! $contentType) {
            return [];
        }

        $callbacks = $this->fieldRegistry->getFields($contentTypeKey, $location);

        $fields = [];
        foreach ($callbacks as $callback) {
            $fields = array_merge($fields, $callback->getFields($contentTypeKey, $location));
        }

        return $fields;

    }

    public function getAvailableLocations(string $contentTypeKey): array
    {
        return $this->fieldRegistry->getAllLocations($contentTypeKey);
    }
}
