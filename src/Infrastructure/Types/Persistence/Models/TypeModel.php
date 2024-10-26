<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Types\Persistence\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $key
 * @property string $entity_class
 * @property string $tenant_id
 * @property string $project_id
 * @property Collection<AllowedRelationModel>|Builder $allowedRelations
 */
class TypeModel extends BaseTenantProjectModel
{
    protected $table = 'types';

    protected $fillable = [
        'id',
        'key',
        'entity_class',
        'tenant_id',
        'project_id',
    ];

    public function allowedRelations(): HasMany
    {
        return $this->hasMany(AllowedRelationModel::class, 'source_type_id');
    }
}
