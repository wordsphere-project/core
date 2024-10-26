<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use WordSphere\Core\Infrastructure\Tenancy\Persistence\Models\EloquentProject;
use WordSphere\Core\Interfaces\Filament\Pages\Tenancy\TenantProjectSelection;

class RequireTenantAndProject
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if ($request->route()->getName() === 'filament.wordsphere.pages.tenant-project-selection') {
            return $next($request);
        }

        // Redirect if either tenant or project is missing
        if (! Session::has('current_tenant_id') || ! Session::has('current_project_id')) {

            /** @var EloquentProject $project */
            $project = Cache::remember('current_tenant_id', 60 * 60, function () {
                return EloquentProject::query()->first();
            });

            Session::put('current_tenant_id', $project->tenant->id);
            Session::put('current_project_id', $project->id);

            //return redirect()->to(TenantProjectSelection::getUrl());
        }

        return $next($request);
    }
}
