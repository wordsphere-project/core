<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Types\Persistence\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use WordSphere\Core\Infrastructure\Shared\Models\TenantProjectModel;

/**
 * @property string $id
 * @property string $name
 * @property string $source_type_id
 * @property string $target_type_id
 * @property string $relation_type
 * @property bool $is_required
 * @property ?int $min_items
 * @property ?int $max_items
 * @property ?string $inverse_relation_name
 * @property ?int $sort_order
 * @property string $tenant_id
 * @property string $project_id
 */
class AllowedRelationModel extends TenantProjectModel
{
    protected $table = 'allowed_relations';

    protected $fillable = [
        'id',
        'name',
        'source_type_id',
        'target_type_id',
        'relation_type',
        'is_required',
        'min_items',
        'max_items',
        'inverse_relation_name',
        'sort_order',
        'tenant_id',
        'project_id',
    ];

    public function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'min_items' => 'integer',
            'max_items' => 'integer',
        ];
    }

    public function sourceType(): BelongsTo
    {
        return $this->belongsTo(TypeModel::class, 'source_type_id');
    }

    public function targetType(): BelongsTo
    {
        return $this->belongsTo(TypeModel::class, 'target_type_id');
    }
}
