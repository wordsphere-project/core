<?php

declare(strict_types=1);

namespace WordSphere\Core;

use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use WordSphere\Core\Commands\InstallCommand;
use WordSphere\Core\Commands\MakeThemeCommand;
use WordSphere\Core\Livewire\Pages\ManageTheme;

class WordSphereServiceProvider extends PackageServiceProvider
{
    public function boot(): void
    {

        $this->registerResources();

    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('wordsphere')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web')
            ->hasMigration('create_migration_table_name_table')
            ->hasCommands(
                InstallCommand::class,
                MakeThemeCommand::class
            );
    }

    private function registerResources(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'wordsphere');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'wordsphere');

        Livewire::component('manage-theme', ManageTheme::class);
    }
}
