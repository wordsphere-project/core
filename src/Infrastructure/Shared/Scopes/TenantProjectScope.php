<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Shared\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;

class TenantProjectScope implements Scope
{
    /**
     * {@inheritDoc}
     */
    public function apply(Builder $builder, Model $model)
    {
        $provider = app(TenantProjectProvider::class);

        $builder->where('tenant_id', $provider->getCurrentTenantId()->toString())
            ->where('project_id', $provider->getCurrentProjectId()->toString());

    }
}
