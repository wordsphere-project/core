<?php

declare(strict_types=1);

namespace WordSphere\Core;

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

use function __;

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
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(
                in: __DIR__.'/Filament/Resources',
                for: 'WordSphere\\Core\\Filament\\Resources'
            )
            ->discoverPages(
                in: __DIR__.'/Filament/Pages',
                for: 'WordSphere\\Core\\Filament\\Pages'
            )
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('Content'),
                NavigationGroup::make('Appearance')
                    ->label(__('Appearance'))
                    ->collapsed(true)
                    ->icon('heroicon-o-paint-brush'),
                NavigationGroup::make('Settings')
                    ->label(__('Settings'))
                    ->collapsed()
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
            ]);
    }
}
