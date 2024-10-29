<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Types\Persistence\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use WordSphere\Core\Infrastructure\Shared\Models\TenantProjectModel;

/**
 * @property string $id
 * @property string $key
 * @property string $entity_class
 * @property string $tenant_id
 * @property string $project_id
 * @property array $interface_data
 * @property array $custom_fields
 * @property Collection<AllowedRelationModel>|Builder $allowedRelations
 */
class TypeModel extends TenantProjectModel
{
    protected $table = 'types';

    protected $fillable = [
        'id',
        'key',
        'entity_class',
        'interface_data'.
        'custom_fields',
        'tenant_id',
        'project_id',
    ];

    public function casts(): array
    {
        return [
            'interface_data' => 'json',
            'custom_fields' => 'json',
        ];
    }

    public function allowedRelations(): HasMany
    {
        return $this->hasMany(AllowedRelationModel::class, 'source_type_id');
    }
}
