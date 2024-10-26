<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Providers;

use Carbon\Laravel\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Panel;

class FilamentClustersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::serving(function (Panel $panel): void {
            $panel->discoverClusters(
                in: __DIR__.'/../Clusters',
                for: 'WordSphere\\Core\\Interfaces\\Filament\\Clusters\\'
            );
        });
    }
}
