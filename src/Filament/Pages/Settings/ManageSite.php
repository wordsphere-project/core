<?php

declare(strict_types=1);

namespace WordSphere\Core\Filament\Pages\Settings;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Str;
use WordSphere\Core\Settings\AppSettings;

class ManageSite extends SettingsPage
{
    protected static ?string $navigationGroup = 'Settings';

    protected static string $settings = AppSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                components: [
                    Section::make()->columns(2)->schema(
                        components: [

                            TextInput::make('name')
                                ->label(__('Site Name'))
                                ->maxLength(255),

                            MarkdownEditor::make('about')
                                ->label(__('About Site'))
                                ->columnSpanFull(),

                            Select::make('theme')
                                ->label('Theme')
                                ->options(
                                    options: [
                                        'default' => 'Default',
                                        'agroglobal' => 'Agroglobal',
                                        'bdynamic' => 'Bdynamic',
                                    ]
                                )
                                ->searchable(),
                            Select::make('timezone')
                                ->label(__('Timezone'))
                                ->options(fn () => collect(timezone_identifiers_list())
                                    ->mapToGroups(
                                        fn ($timezone) => [
                                            Str::of($timezone)
                                                ->before('/')
                                                ->toString() => [$timezone => $timezone],
                                        ]
                                    )
                                    ->map(fn ($group) => $group->collapse()))
                                ->searchable()
                                ->suffixIcon('heroicon-o-globe-alt'),

                            Grid::make(2)
                                ->schema(
                                    components: [
                                        Toggle::make('active')
                                            ->label(__('Site Active')),
                                    ]
                                ),
                        ]
                    ),
                ]);
    }
}
