<?php

declare(strict_types=1);


use Filament\FilamentServiceProvider;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Livewire\LivewireServiceProvider;
use WordSphere\Core\WordSphereDashboardServiceProvider;
use WordSphere\Core\WordSphereServiceProvider;
use Workbench\App\Providers\WorkbenchServiceProvider;


return [
    WorkbenchServiceProvider::class,
    WordSphereServiceProvider::class,
    WordSphereDashboardServiceProvider::class,
    LivewireServiceProvider::class
];
