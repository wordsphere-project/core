<?php

declare(strict_types=1);

namespace WordSphere\Tests;

use Awcodes\Curator\CuratorServiceProvider;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\SpatieLaravelSettingsPluginServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider;
use Illuminate\Broadcasting\BroadcastServiceProvider;
use Illuminate\Bus\BusServiceProvider;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Concurrency\ConcurrencyServiceProvider;
use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Providers\ConsoleSupportServiceProvider;
use Illuminate\Foundation\Providers\FoundationServiceProvider;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Hashing\HashServiceProvider;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Pipeline\PipelineServiceProvider;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Attributes\WithEnv;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;
use Spatie\LaravelSettings\LaravelSettingsServiceProvider;
use Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository;
use Spatie\Permission\Models\Role;
use WordSphere\Core\Domain\UserManagement\Enums\SystemGuard;
use WordSphere\Core\Infrastructure\Identity\Persistence\UserModel;
use WordSphere\Core\Legacy\Enums\SystemRole;
use WordSphere\Core\Legacy\Settings\AppSettings;
use WordSphere\Core\WordSphereDashboardServiceProvider;
use WordSphere\Core\WordSphereServiceProvider;

use function database_path;
use function dump;
use function Orchestra\Testbench\package_path;

#[WithEnv('DB_CONNECTION', 'testing')]
class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;

    protected UserModel $superAdmin;

    protected function setUp(): void
    {

        parent::setUp();
        Factory::guessFactoryNamesUsing(
            fn (string $modelName): string => 'WordSphere\\Core\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->setTheme();
        $this->createSuperAdminUSer();
        $this->actingAs(
            user: $this->superAdmin
        );

    }

    private function createSuperAdminUSer(): void
    {

        $this->superAdmin = UserModel::factory()->create();
        $superAdmin = Role::findByName(
            name: SystemRole::SUPER_ADMIN->value,
            guardName: SystemGuard::WEB->value
        );

        $this->superAdmin->assignRole(
            roles: $superAdmin
        );
    }

    private function setTheme(): void
    {
        AppSettings::fake(
            values: [
                'theme' => 'wordsphere/orbit-theme',
            ],
            loadMissingValues: true
        );
    }

    final public static function applicationBasePath(): string
    {
        return package_path('workbench');
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set([
            'auth.providers.users.model' => 'Workbench\\App\\User',
            'database.default' => 'testing',
            'database.migrations' => 'db_migration',
            'cache' => [
                'default' => 'array',
                'stores' => [
                    'array' => [
                        'driver' => 'array',
                        'serialize' => false,
                    ],
                ],
            ],

            'settings' => [
                'migrations_paths' => [
                    database_path('settings'),
                ],
                'default_repository' => 'database',
                'repositories' => [
                    'database' => [
                        'type' => DatabaseSettingsRepository::class,
                        'model' => null,
                        'table' => null,
                        'connection' => null,
                    ],
                ],
                'cache' => [
                    'enables' => false,
                ],
            ],
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
            TablesServiceProvider::class,
            FormsServiceProvider::class,
            FilamentServiceProvider::class,
            SupportServiceProvider::class,
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            InfolistsServiceProvider::class,
            NotificationsServiceProvider::class,
            WidgetsServiceProvider::class,
            DatabaseServiceProvider::class,
            ViewServiceProvider::class,
            FilesystemServiceProvider::class,
            CacheServiceProvider::class,
            LivewireServiceProvider::class,
            SessionServiceProvider::class,
            AuthServiceProvider::class,
            BroadcastServiceProvider::class,
            BusServiceProvider::class,
            ConsoleSupportServiceProvider::class,
            ConcurrencyServiceProvider::class,
            CookieServiceProvider::class,
            EncryptionServiceProvider::class,
            FilesystemServiceProvider::class,
            FoundationServiceProvider::class,
            HashServiceProvider::class,
            MailServiceProvider::class,
            NotificationsServiceProvider::class,
            PaginationServiceProvider::class,
            PasswordResetServiceProvider::class,
            PipelineServiceProvider::class,
            QueueServiceProvider::class,
            TranslationServiceProvider::class,
            ValidationServiceProvider::class,
            WordSphereDashboardServiceProvider::class,
            WordSphereServiceProvider::class,
            SpatieLaravelSettingsPluginServiceProvider::class,
            LaravelSettingsServiceProvider::class,
            CuratorServiceProvider::class,
        ];
    }
}
