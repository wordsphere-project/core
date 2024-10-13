<?php

declare(strict_types=1);

use Livewire\LivewireServiceProvider;
use WordSphere\Core\WordSphereDashboardServiceProvider;
use WordSphere\Core\WordSphereServiceProvider;
use Workbench\App\Providers\WorkbenchServiceProvider;

return [
    WordSphereServiceProvider::class,
    WordSphereDashboardServiceProvider::class,
    WorkbenchServiceProvider::class,
    LivewireServiceProvider::class,
];
