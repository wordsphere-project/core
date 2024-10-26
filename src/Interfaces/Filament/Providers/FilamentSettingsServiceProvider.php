<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Providers;

use Carbon\Laravel\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Panel;

class FilamentSettingsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::serving(function (Panel $panel): void {
            $panel->discoverResources(
                in: __DIR__.'/../Clusters/Settings/Resources',
                for: 'WordSphere\\Core\\Interfaces\\Filament\\Clusters\\Settings\\Resources\\'
            );

        });
    }
}
