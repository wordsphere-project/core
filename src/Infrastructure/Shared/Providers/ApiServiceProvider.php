<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Shared\Providers;

use Illuminate\Support\ServiceProvider;
use WordSphere\Core\Interfaces\Console\Commands\Api\GenerateApiKey;

class ApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        if ($this->app->runningInConsole()) {
            $this->registerConsoleCommand();
        }

    }

    private function registerConsoleCommand(): void
    {
        $this->commands([
            GenerateApiKey::class,
        ]);
    }
}
