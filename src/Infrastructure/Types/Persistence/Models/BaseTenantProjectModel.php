<?php

namespace WordSphere\Core\Infrastructure\Types\Persistence\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;

class BaseTenantProjectModel extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected static function booted(): void
    {
        static::addGlobalScope('tenant_project', function (Builder $builder): void {
            $provider = app(TenantProjectProvider::class);

            $builder->where('tenant_id', $provider->getCurrentTenantId())
                ->where('project_id', $provider->getCurrentProjectId());
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
}
