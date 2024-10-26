<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Tenancy\Persistence\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $name
 * @property string $type
 * @property array $domains
 * @property string $tenant_id
 * @property EloquentTenant $tenant
 */
class EloquentProject extends Model
{
    use HasUuids;

    public $table = 'projects';

    public function casts(): array
    {
        return [
            'domains' => 'json',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(EloquentTenant::class);
    }
}
