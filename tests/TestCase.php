<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\View\ViewServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Attributes\WithEnv;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use WordSphere\Core\WordSphereServiceProvider;

use function base_path;
use function Orchestra\Testbench\package_path;
use function realpath;
use function storage_path;

#[WithEnv('DB_CONNECTION', 'testing')]
class TestCase extends Orchestra
{
    use RefreshDatabase;
    use WithWorkbench;

    protected function setUp(): void
    {

        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'WordSphere\\Core\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

    }


    final public static function applicationBasePath(): string
    {
        return package_path('workbench');
    }

    /**
     * @param $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set([
            'auth.providers.users.model' => 'Workbench\\App\\User',
            'database.default' => 'testing',
            'database.migrations' => 'db_migration',
            'view.compiled' => realpath(storage_path('framework/views')),
            'view.paths' => [
                realpath(base_path('resources/vies'))
            ],
            'cache.stores' => [
              'array' => [
                  'driver' => 'array',
                  'serialize' => false,
              ]
            ]
        ]);

    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(
            package_path('workbench/database/migrations')
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            ViewServiceProvider::class,
            FilesystemServiceProvider::class,
            CacheServiceProvider::class,
            LivewireServiceProvider::class,
            WordSphereServiceProvider::class,
        ];
    }
}
