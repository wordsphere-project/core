<?php

namespace WordSphere\Core\Infrastructure\Shared\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;

/**
 * @property string $tenant_id
 * @property string $project_id
 */
class TenantProjectModel extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected static function booted(): void
    {
        static::addGlobalScope('tenant_project', function (Builder $builder): void {
            $provider = app(TenantProjectProvider::class);

            $table = $builder->getModel()->getTable();

            $builder->where("{$table}.tenant_id", $provider->getCurrentTenantId()->toString())
                ->where("{$table}.project_id", $provider->getCurrentProjectId()->toString());
        });

        static::creating(function (Model $model): void {
            if (! $model->getAttribute('id')) {
                $model->setAttribute('id', Uuid::generate()->toString());
            }

            $provider = app(TenantProjectProvider::class);
            $model->setAttribute('tenant_id', $provider->getCurrentTenantId()->toString());
            $model->setAttribute('project_id', $provider->getCurrentProjectId()->toString());
        });
    }

    public function getTenantId(): Uuid
    {
        return Uuid::fromString($this->tenant_id);
    }

    public function getProjectId(): Uuid
    {
        return Uuid::fromString($this->project_id);
    }
}
