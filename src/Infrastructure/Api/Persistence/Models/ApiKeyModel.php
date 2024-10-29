<?php

namespace WordSphere\Core\Infrastructure\Api\Persistence\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $tenant_id
 * @property string $project_id
 * @property string $key
 * @property string $name
 * @property bool $active
 * @property Carbon $expires_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
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
