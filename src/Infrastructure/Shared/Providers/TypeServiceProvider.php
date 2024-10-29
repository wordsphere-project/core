<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Shared\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use WordSphere\Core\Domain\Types\Repositories\TypeRepositoryInterface;
use WordSphere\Core\Domain\Types\TypeRegistry;
use WordSphere\Core\Infrastructure\Types\Persistence\Cache\CachedTypeRepository;
use WordSphere\Core\Infrastructure\Types\Persistence\Repositories\EloquentTypeRepository;
use WordSphere\Core\Infrastructure\Types\Services\EntityModelMapper;
use WordSphere\Core\Interfaces\Console\Commands\Types\RegisterTypes;
use WordSphere\Core\Interfaces\Filament\Builders\TypeNavigationBuilder;
use WordSphere\Core\Interfaces\Filament\Types\TypeFieldRegistry;

class TypeServiceProvider extends ServiceProvider
{
    private array $registrars = [];

    public function register(): void
    {

        $this->app->bind(TypeRepositoryInterface::class, function ($app) {
            return new CachedTypeRepository(
                new EloquentTypeRepository,
                $app['cache.store']
            );
        });

        $this->app->singleton(TypeRegistry::class);
        $this->app->singleton(TypeFieldRegistry::class);
        $this->app->singleton(TypeNavigationBuilder::class);
        $this->app->singleton(EntityModelMapper::class);
    }

    public function boot(): void
    {

        if ($this->app->runningInConsole()) {
            $this->registerConsoleCommand();
        }

        if (! $this->app->runningInConsole() || ! str_contains(request()->server('argv')[1] ?? '', 'migrate')) {
            $this->app->booted(function () {
                $this->registerTypes();
            });
        }

    }

    private function registerTypes(): void
    {

        if (! Schema::hasTable('types')) {
            return;
        }

        // Phase 1: Register all types
        $registrars = config('types.registrars', []);

        foreach ($registrars as $registrarClass) {
            if (class_exists($registrarClass)) {
                $registrar = $this->app->make($registrarClass);
                $registrar->register();
                $this->registrars[] = $registrar;
            }
        }

        // Phase 2: Process all pending relations
        foreach ($this->registrars as $registrar) {
            $registrar->processPendingRelations();
        }
    }

    private function registerConsoleCommand(): void
    {
        $this->commands([
            RegisterTypes::class,
        ]);
    }
}
