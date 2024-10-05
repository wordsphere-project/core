<?php

declare(strict_types=1);

namespace WordSphere\Core;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Awcodes\Curator\CuratorPlugin;
use Exception;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use WordSphere\Core\Settings\AppSettings;
use function __;
use function app_path;
use function config;

class WordSphereDashboardServiceProvider extends PanelProvider
{
    /**
     * @throws Exception
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('wordsphere')
            ->path('admin')
            ->default(false)
            ->login()
            ->authPasswordBroker('users')
            ->emailVerification()
            ->passwordReset()
            ->profile()
            ->sidebarCollapsibleOnDesktop()
            ->brandName(app(AppSettings::class)->name)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(
                in: __DIR__.'/Filament/Resources',
                for: 'WordSphere\\Core\\Filament\\Resources'
            )
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->discoverPages(
                in: __DIR__.'/Filament/Pages',
                for: 'WordSphere\\Core\\Filament\\Pages'
            )
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('CMS')
                    ->label(__('CMS'))
                    ->collapsed(false)
                    ->icon('heroicon-o-newspaper'),
                NavigationGroup::make('Appearance')
                    ->label(__('Appearance'))
                    ->collapsed(true)
                    ->icon('heroicon-o-paint-brush'),
                NavigationGroup::make('Settings')
                    ->label(__('Settings'))
                    ->collapsed(true)
                    ->icon('heroicon-o-cog-6-tooth'),

            ])
            ->discoverWidgets(
                in: __DIR__.'/Filament/Widgets',
                for: 'WordSphere\\Core\\Filament\\Widgets'
            )
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins(
                plugins: [
                    FilamentSpatieRolesPermissionsPlugin::make(),
                    CuratorPlugin::make()
                        ->label('Media')
                        ->pluralLabel('Media')
                        ->navigationIcon('heroicon-o-photo')
                        ->navigationGroup('Content')
                        ->navigationSort(3)
                        ->navigationCountBadge()
                        ->registerNavigation(true)
                        ->defaultListView('list'),
                ]
            );
    }
}
