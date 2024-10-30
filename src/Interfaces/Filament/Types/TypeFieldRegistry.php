<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Types;

use Closure;
use WordSphere\Core\Domain\Types\ValueObjects\CustomField;
use WordSphere\Core\Domain\Types\ValueObjects\FieldGroup;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;

use function array_map;

class TypeFieldRegistry
{
    private array $fieldDefinitions = [];

    private array $fieldRenderers = [];

    private array $groupRenderers = [];

    private array $validationRules = [];

    public function registerFields(TypeKey $typeKey, string $location, Closure $definitionCallback): void
    {
        if (! isset($this->fieldDefinitions[$typeKey->toString()])) {
            $this->fieldDefinitions[$typeKey->toString()] = [];
        }

        if (! isset($this->fieldDefinitions[$typeKey->toString()][$location])) {
            $this->fieldDefinitions[$typeKey->toString()][$location] = [];
        }

        $this->fieldDefinitions[$typeKey->toString()][$location][] = $definitionCallback;
    }

    public function registerFieldRenderer(string $fieldType, Closure $renderer): void
    {
        $this->fieldRenderers[$fieldType] = $renderer;
    }

    public function getFieldDefinitions(TypeKey $typeKey, string $location): array
    {
        $definitions = [];
        foreach ($this->fieldDefinitions[$typeKey->toString()][$location] ?? [] as $callback) {
            $fields = $callback();
            foreach ($fields as $field) {
                $definitions[] = new CustomField(
                    $field['key'],
                    $field['type'],
                    $field['config'] ?? []
                );
            }
        }

        return $definitions;
    }

    public function renderField(CustomField $field): mixed
    {
        if (! isset($this->fieldRenderers[$field->getType()])) {
            throw new \InvalidArgumentException("No renderer registered for field type: {$field->getType()}");
        }

        return ($this->fieldRenderers[$field->getType()])($field);
    }

    public function getFields(TypeKey $typeKey, string $location): array
    {
        return $this->fieldDefinitions[$typeKey->toString()][$location] ?? [];
    }

    public function registerGroupRenderer(TypeKey $fieldType, Closure $renderer): void
    {
        $this->groupRenderers[$fieldType->toString()] = $renderer;
    }

    public function registerValidationRule(string $ruleName, Closure $validator): void
    {
        $this->validationRules[$ruleName] = $validator;
    }

    public function renderGroup(FieldGroup $group): mixed
    {
        if (! isset($this->groupRenderers[$group->getType()])) {
            throw new \InvalidArgumentException("No renderer registered for field group: {$group->getType()}");
        }

        $renderedFields = array_map(
            fn ($field) => $field instanceof CustomField ? $this->renderField($field) : $this->renderGroup($field),
            $group->getFields()
        );

        return ($this->groupRenderers[$group->getType()])($renderedFields, $group->getConfig());

    }

    public function getValidationRules(CustomField $field): array
    {
        $rules = [];
        foreach ($field->getValidation() as $ruleName => $config) {
            if (! isset($this->validationRules[$ruleName])) {
                throw new \InvalidArgumentException("Unknown validation rule: {$ruleName}");
            }
            $rules[] = ($this->validationRules[$ruleName])($config);
        }

        return $rules;
    }

    public function getAllLocations(TypeKey $typeKey): array
    {
        return array_keys($this->fields[$typeKey->toString()] ?? []);
    }
}
