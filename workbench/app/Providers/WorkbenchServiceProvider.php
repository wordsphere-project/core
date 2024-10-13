<?php

declare(strict_types=1);

namespace Workbench\App\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Workbench\App\Themes\CustomFields\HomePageCustomFields;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Model::unguard();
        $this->registerCustomFields();

    }

    /**
     * @throws BindingResolutionException
     */
    private function registerCustomFields(): void
    {
        HomePageCustomFields::forGeneralTab();
        HomePageCustomFields::forAboutUsTab();
    }
}
