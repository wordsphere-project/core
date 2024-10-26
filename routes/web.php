<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use WordSphere\Core\Interfaces\Filament\Pages\Tenancy\TenantProjectSelection;
use WordSphere\Core\Legacy\Livewire\Pages\ManageTheme;

Route::middleware(['auth'])->group(function (): void {

    Route::get('admin/manage-theme/{theme}', ManageTheme::class);

    Route::get('/admin/select-tenant-project', [TenantProjectSelection::class, 'index'])
        ->name('admin.tenant-project.select');
});
