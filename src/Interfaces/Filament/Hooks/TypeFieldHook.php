<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Hooks;

use WordSphere\Core\Domain\Types\TypeRegistry;
use WordSphere\Core\Domain\Types\ValueObjects\CustomField;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Interfaces\Filament\Types\TypeFieldRegistry;

use function array_merge;

readonly class TypeFieldHook
{
    public function __construct(
        private TypeFieldRegistry $fieldRegistry,
        private TypeRegistry $registry,
    ) {}

    public function getCustomFields(string $typeKey, string $location): array
    {
        $type = $this->registry->get(TypeKey::fromString($typeKey));
        if (! $type) {
            return [];
        }

        $fields = [];
        foreach ($this->fieldRegistry->getFields(TypeKey::fromString($typeKey), $location) as $locationFieldsCallBack) {

            $locationFields = $locationFieldsCallBack();

            foreach ($locationFields as $field) {
                $fields = array_merge($fields, [$this->fieldRegistry->renderField(CustomField::fromArray($field))]);
            }
        }

        return $fields;
    }

    public function getAvailableLocations(string $typeKey): array
    {
        return $this->fieldRegistry->getAllLocations(TypeKey::fromString($typeKey));
    }
}
