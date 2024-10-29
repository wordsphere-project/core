<?php

declare(strict_types=1);

namespace WordSphere\Core;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use WordSphere\Core\Infrastructure\Shared\Providers\ApiServiceProvider;
use WordSphere\Core\Infrastructure\Shared\Providers\ContentServiceProvider;
use WordSphere\Core\Infrastructure\Shared\Providers\EventServiceProvider;
use WordSphere\Core\Infrastructure\Shared\Providers\TypeServiceProvider;
use WordSphere\Core\Interfaces\Filament\Providers\FilamentTypeFieldServiceProvider;
use WordSphere\Core\Legacy\Commands\InstallCommand;
use WordSphere\Core\Legacy\Commands\MakeThemeCommand;
use WordSphere\Core\Legacy\Contracts\CustomFieldsManagerContract;
use WordSphere\Core\Legacy\Livewire\Pages\ManageTheme;
use WordSphere\Core\Legacy\Support\CustomFields\CustomFieldsManager;

use function public_path;

class WordSphereServiceProvider extends PackageServiceProvider
{
    public function register(): void
    {
        parent::register();
        $this->registerProviders();
        $this->bindCustomFieldsManager();
    }

    public function boot(): void
    {
        parent::boot();
        $this->registerResources();
        $this->publishAssets();

    }

    private function registerProviders(): void
    {

        $this->app->register(provider: EventServiceProvider::class);
        $this->app->register(provider: ApiServiceProvider::class);
        $this->app->register(provider: FilamentTypeFieldServiceProvider::class);
        $this->app->register(provider: TypeServiceProvider::class);
        $this->app->register(provider: ContentServiceProvider::class);

        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }

    private function bindCustomFieldsManager(): void
    {

        $this->app->scoped(
            abstract: CustomFieldsManagerContract::class,
            concrete: CustomFieldsManager::class,
        );
    }

    private function publishAssets(): void
    {
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/wordsphere'),
        ], 'wordsphere-assets');
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
            ->hasConfigFile([
                'wordsphere',
                'types',
                'permission',
                'curator',
            ])
            ->hasViews()
            ->hasAssets()
            ->hasRoute('web')
            ->hasRoute('api')
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
