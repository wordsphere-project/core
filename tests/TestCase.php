<?php

declare(strict_types=1);

namespace WordSphere\Core\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JetBrains\PhpStorm\NoReturn;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use WordSphere\Core\WordSphereServiceProvider;

use function Orchestra\Testbench\package_path;

abstract class TestCase extends Orchestra
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

    #[NoReturn]
    final public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_skeleton_table.php.stub';
        $migration->up();
        */
    }

    protected function getPackageProviders($app): array
    {
        return [
            WordSphereServiceProvider::class,
        ];
    }
}
