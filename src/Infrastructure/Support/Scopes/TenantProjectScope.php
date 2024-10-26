<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Support\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

class TenantProjectScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (Session::has('current_tenant_id')) {
            $builder->where('tenant_id', Session::get('current_tenant_id'));
        }

        if (Session::has('current_project_id')) {
            $builder->where('project_id', Session::get('current_project_id'));
        }
    }
}
