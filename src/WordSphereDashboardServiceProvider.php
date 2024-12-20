<?php

declare(strict_types=1);

namespace WordSphere\Core;

use Awcodes\Curator\CuratorPlugin;
use Awcodes\Curator\Resources\MediaResource;
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
use WordSphere\Core\Interfaces\Filament\Builders\TypeNavigationBuilder;
use WordSphere\Core\Interfaces\Filament\Middleware\RequireTenantAndProject;
use WordSphere\Core\Interfaces\Filament\Middleware\ValidateType;

use function __;
use function app_path;

class WordSphereDashboardServiceProvider extends PanelProvider
{
    /**
     * @throws Exception
     */
    public function panel(Panel $panel): Panel
    {
        $typeNavigationBuilder = app(TypeNavigationBuilder::class);

        return $panel
            ->id('wordsphere')
            ->default()
            ->spa()
            ->font('switzer', 'https://fonts.cdnfonts.com/css/switzer')
            ->path('admin')
            ->login()
            ->authGuard('web')
            ->authPasswordBroker('users')
            //->emailVerification()
            ->passwordReset()
            ->profile()
            ->sidebarCollapsibleOnDesktop()
            ->brandName(config('app.name'))
            ->viteTheme('resources/css/filament/admin/wordsphere.css', 'vendor/wordsphere/build')
            ->discoverClusters(in: __DIR__.'/Interfaces/Filament/Clusters', for: 'WordSphere\\Core\\Interfaces\\Filament\\Clusters')
            ->discoverResources(in: __DIR__.'/Interfaces/Filament/Resources', for: 'WordSphere\\Core\\Interfaces\\Filament\\Resources')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: __DIR__.'/Interfaces/Filament/Pages', for: 'WordSphere\\Core\\Interfaces\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(
                in: __DIR__.'/Filament/Widgets',
                for: 'WordSphere\\Core\\Filament\\Widgets'
            )
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->navigationItems(
                items: [
                    //...$typeNavigationBuilder->build()
                ]
            )
            ->navigationGroups([
                NavigationGroup::make('Pages')
                    ->label(__('pages.pages'))
                    ->collapsible(true)
                    ->icon('heroicon-o-newspaper'),
                NavigationGroup::make('Appearance')
                    ->label(__('Appearance'))
                    ->collapsed(true)
                    ->icon('heroicon-o-paint-brush'),
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
                ValidateType::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                RequireTenantAndProject::class,
            ])
            ->plugins([
                CuratorPlugin::make()
                    ->label('Media')
                    ->pluralLabel('Media')
                    ->navigationIcon('heroicon-o-photo')
                    ->navigationSort(3)
                    ->navigationCountBadge(true)
                    ->registerNavigation(true)
                    ->resource(MediaResource::class)
                    ->defaultListView('grid'),
            ]);

    }
}
