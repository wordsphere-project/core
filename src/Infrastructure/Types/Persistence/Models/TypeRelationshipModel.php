<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Types\Persistence\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use WordSphere\Core\Infrastructure\Shared\Models\TenantProjectModel;

/**
 * @property string $id
 */
class TypeRelationshipModel extends TenantProjectModel
{
    use HasUuids;

    protected $table = 'type_relationships';

    protected $fillable = [
        'id',
        'source_id',
        'source_type',
        'target_id',
        'target_type',
        'relation_name',
        'sort_order',
        'tenant_id',
        'project_id',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function source(): MorphTo
    {
        return $this->morphTo('source');
    }

    public function target(): MorphTo
    {
        return $this->morphTo('target');
    }
}
