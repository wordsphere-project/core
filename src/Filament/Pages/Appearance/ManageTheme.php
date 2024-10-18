<?php

declare(strict_types=1);

namespace WordSphere\Core\Filament\Pages\Appearance;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use WordSphere\Core\Legacy\Support\Themes\ThemeManager;

/**
 * @property Form $form
 */
class ManageTheme extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationGroup = 'Appearance';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'wordsphere::filament.pages.appearance.manage-theme';

    public function mount(ThemeManager $themeManager): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                components: [
                    Section::make('General')
                        ->label(__('General Data'))
                        ->columns(2)
                        ->schema(
                            components: [
                                TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required()
                                    ->maxLength(255),
                                ColorPicker::make('color')
                                    ->label(__('Color')),

                            ]
                        ),
                ]
            );
    }
}
