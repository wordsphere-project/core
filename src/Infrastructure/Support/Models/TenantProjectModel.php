<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use WordSphere\Core\Infrastructure\Support\Scopes\TenantProjectScope;

abstract class TenantProjectModel extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantProjectScope);

        static::created(function ($model) {
            if (! $model->tenant_id && Session::has('current_tenant_id')) {
                $model->tenant_id = Session::get('current_tenant_id');
            }
            if (! $model->project_id && Session::has('current_project_id')) {
                $model->project_id = Session::get('current_project_id');
            }
        });
    }
}
