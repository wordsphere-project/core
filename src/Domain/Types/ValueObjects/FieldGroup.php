<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Types\ValueObjects;

use function is_array;

class FieldGroup
{
    public function __construct(
        private readonly string $type,
        private readonly array $fields,
        private readonly array $config = []
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'fields' => array_map(
                fn ($field) => $field instanceof CustomField ? $field->toArray() : $field,
                $this->fields
            ),
            'config' => $this->config,
        ];
    }

    public static function fromArray(array $data): self
    {
        $fields = array_map(
            fn ($fieldData) => is_array($fieldData) ? CustomField::fromArray($fieldData) : $fieldData,
            $data['fields']
        );

        return new self(
            $data['type'],
            $fields,
            $data['config'] ?? []
        );
    }
}
