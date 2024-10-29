<?php

namespace WordSphere\Core\Infrastructure\Api\Persistence\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $tenant_id
 * @property string $project_id
 */
class ApiKeyModel extends Model
{
    use HasUuids;

    protected $table = 'api_keys';

    protected $fillable = [
        'key',
        'tenant_id',
        'project_id',
        'name',
        'active',
        'expires_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'expires_at' => 'datetime',
    ];
}
