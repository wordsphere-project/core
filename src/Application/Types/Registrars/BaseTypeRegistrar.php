<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\Types\Registrars;

use Closure;
use WordSphere\Core\Domain\Types\Entities\Type;
use WordSphere\Core\Domain\Types\Enums\RelationType;
use WordSphere\Core\Domain\Types\TypeRegistry;
use WordSphere\Core\Domain\Types\ValueObjects\AllowedRelation;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Interfaces\Filament\Types\TypeFieldRegistry;

abstract class BaseTypeRegistrar
{
    protected array $pendingRelations = [];

    protected TypeRegistry $registry;

    protected TypeFieldRegistry $fieldRegistry;

    public function __construct(
        TypeRegistry $registry,
        TypeFieldRegistry $fieldRegistry
    ) {
        $this->registry = $registry;
        $this->fieldRegistry = $fieldRegistry;
    }

    abstract public function register(): void;

    protected function addFields(TypeKey $typeKey, string $location, Closure $callback): void
    {

        $this->fieldRegistry->registerFields($typeKey, $location, $callback);

    }

    protected function addRelation(
        Type $sourceType,
        string $name,
        TypeKey $targetTypeKey,
        RelationType $relationType,
        bool $required = false,
        ?int $minItems = null,
        ?int $maxItems = null,
        ?string $inverseRelationName = null,
        ?string $orderColumn = 'sort_order',
    ): void {

        $this->pendingRelations[] = [
            'sourceType' => $sourceType,
            'name' => $name,
            'targetTypeKey' => $targetTypeKey,
            'relationType' => $relationType,
            'required' => $required,
            'minItems' => $minItems,
            'maxItems' => $maxItems,
            'inverseRelationName' => $inverseRelationName,
            'orderColumn' => $orderColumn,
        ];

    }

    public function processPendingRelations(): void
    {
        foreach ($this->pendingRelations as $relation) {
            $targetTypeKey = $this->registry->get($relation['targetTypeKey']);

            if ($targetTypeKey) {

                $allowedRelation = new AllowedRelation(
                    name: $relation['name'],
                    sourceType: $relation['sourceType'],
                    targetType: $targetTypeKey,
                    relationType: $relation['relationType'],
                    isRequired: $relation['required'],
                    minItems: $relation['minItems'],
                    maxItems: $relation['maxItems'],
                    inverseRelationName: $relation['inverseRelationName'],
                    orderColumn: $relation['orderColumn'],
                );

                $relation['sourceType']->addAllowedRelation($allowedRelation);
                $this->registry->update($relation['sourceType']);

            }

        }
    }
}
