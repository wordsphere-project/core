<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Tenancy\Persistence\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 */
class EloquentTenant extends Model
{
    use HasUuids;

    public $table = 'tenants';
}
