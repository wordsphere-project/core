<?php

declare(strict_types=1);

namespace WordSphere\Core\Filament\Pages\Appearance;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use WordSphere\Core\Legacy\Settings\AppSettings;
use WordSphere\Core\Legacy\Support\Themes\ThemeManager;

class Themes extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $themes = null;

    protected static ?string $navigationGroup = 'Appearance';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'wordsphere::filament.pages.appearance.themes';

    public function mount(ThemeManager $themeManager): void
    {

        $this->themes = $themeManager->getThemes();
    }

    public function activateThemeAction(): Action
    {
        return Action::make('activateTheme')
            ->label(__('Activate Theme'))
            ->action(function (AppSettings $settings, array $arguments) {
                $settings->theme = $arguments['namespace'];
                $settings->save();
            })
            ->size('sm')
            ->after(function () {
                Notification::make()
                    ->title(__('Theme Activated'))
                    ->success()
                    ->send();
            });
    }
}
