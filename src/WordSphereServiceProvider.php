<?php

declare(strict_types=1);

namespace WordSphere\Core;

use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use WordSphere\Core\Commands\InstallCommand;
use WordSphere\Core\Commands\MakeThemeCommand;
use WordSphere\Core\Contracts\CustomFieldsManagerContract;
use WordSphere\Core\Livewire\Pages\ManageTheme;

use WordSphere\Core\Support\CustomFields\CustomFieldsManager;
use function config;
use function public_path;

class WordSphereServiceProvider extends PackageServiceProvider
{
    public function register(): void
    {
        parent::register();
        $this->bindCustomFieldsManager();
    }

    public function boot(): void
    {
        parent::boot();
        $this->setPermissionsConfig();
        $this->setCuratorConfig();
        $this->registerResources();
        $this->publishAssets();
    }



    private function bindCustomFieldsManager(): void
    {

        $this->app->scoped(
            abstract: CustomFieldsManagerContract::class,
            concrete: CustomFieldsManager::class,
        );
    }

    private function setCuratorConfig(): void
    {
        config()->set('curator', config('wordsphere.curator'));
    }

    private function setPermissionsConfig(): void
    {
        config()->set('permission', config('wordsphere.permission'));
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
            ->hasConfigFile()
            ->hasViews()
            ->hasAssets()
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
