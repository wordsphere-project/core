<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Types\Persistence\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
 */
class RelationshipModel extends BaseTenantProjectModel
{
    protected $table = 'relationships';

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
