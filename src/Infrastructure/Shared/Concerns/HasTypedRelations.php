<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Shared\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use WordSphere\Core\Domain\Types\Entities\Type;
use WordSphere\Core\Domain\Types\Enums\RelationType;

trait HasTypedRelations
{
    public function getRelationshipsByType(string $relationName, Type $type): HasMany|BelongsTo|BelongsToMany
    {

        $allowedRelations = $type->getAllowedRelations();
        $relation = $allowedRelations[$relationName] ?? null;

        if (! $relation) {
            throw new \InvalidArgumentException("Relation {$relationName} not found for type {$this->type}");
        }

        // Get the target model class from the target type's entity class

        return match ($relation->getRelationType()) {
            RelationType::BELONGS_TO => $this->buildBelongsToRelation(
                self::class,
                $relationName
            ),

            RelationType::BELONGS_TO_MANY => $this->buildBelongsToManyRelation(
                self::class,
                $relationName,
                $relation->getOrderColumn() ?? null
            ),

            RelationType::ONE_TO_MANY => $this->buildOneToManyRelation(
                self::class,
                $relationName
            ),

            RelationType::MANY_TO_MANY => $this->buildManyToManyRelation(
                self::class,
                $relationName,
                $relation->getOrderColumn() ?? null
            ),

            default => throw new \InvalidArgumentException("Unsupported relation type: {$relation->getRelationType()->value}")
        };
    }

    protected function buildBelongsToRelation(string $related, string $relationName): BelongsTo
    {
        return $this->belongsTo($related, "{$relationName}_id")
            ->where('tenant_id', $this->tenant_id)
            ->where('project_id', $this->project_id);
    }

    protected function buildBelongsToManyRelation(
        string $related,
        string $relationName,
        ?string $orderColumn = null
    ): BelongsToMany {
        $relation = $this->belongsToMany(
            $related,
            'type_relationships',
            'source_id',
            'target_id'
        );

        // Use wherePivot for columns in the pivot table
        $relation->wherePivot('relation_name', $relationName)
            ->wherePivot('source_type', $this->type)
            ->wherePivot('tenant_id', $this->tenant_id)
            ->wherePivot('project_id', $this->project_id);

        // Include pivot columns we want to access
        $relation->withPivot([
            'relation_name',
            'source_type',
            'target_type',
            'sort_order',
            'meta_data',
            'tenant_id',
            'project_id',
        ]);

        // Add ordering if specified
        if ($orderColumn) {
            $relation->orderByPivot($orderColumn, 'asc');
        }

        return $relation;
    }

    protected function buildOneToManyRelation(string $related, string $relationName): HasMany
    {
        return $this->hasMany($related, 'parent_id')
            ->where('relation_name', $relationName)
            ->where('tenant_id', $this->tenant_id)
            ->where('project_id', $this->project_id);
    }

    protected function buildManyToManyRelation(
        string $related,
        string $relationName,
        ?string $orderColumn = null
    ): BelongsToMany {
        $relation = $this->belongsToMany(
            $related,
            'type_relationships',
            'source_id',
            'target_id'
        );

        // Use wherePivot for columns in the pivot table
        $relation->wherePivot('relation_name', $relationName)
            ->wherePivot('source_type', $this->type)
            ->wherePivot('tenant_id', $this->tenant_id)
            ->wherePivot('project_id', $this->project_id);

        // Include pivot columns we want to access
        $relation->withPivot([
            'relation_name',
            'source_type',
            'target_type',
            'sort_order',
            'meta_data',
            'tenant_id',
            'project_id',
        ]);

        // Add ordering if specified
        if ($orderColumn) {
            $relation->orderByPivot($orderColumn, 'asc');
        }

        return $relation;
    }
}
