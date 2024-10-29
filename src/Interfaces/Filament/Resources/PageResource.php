<?php

namespace WordSphere\Core\Interfaces\Filament\Resources;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentPage;
use WordSphere\Core\Interfaces\Filament\Resources\PageResource\Form\FormCompiler;
use WordSphere\Core\Interfaces\Filament\Resources\PageResource\Pages\CreatePage;
use WordSphere\Core\Interfaces\Filament\Resources\PageResource\Pages\EditPage;
use WordSphere\Core\Interfaces\Filament\Resources\PageResource\Pages\ListPages;
use WordSphere\Core\Legacy\Enums\ContentStatus;
use WordSphere\Core\Legacy\Support\CustomFields\CustomFieldsManager;
use WordSphere\Core\Legacy\Support\Themes\ThemeManager;

use function __;
use function now;

class PageResource extends Resource
{
    protected static ?string $model = EloquentPage::class;

    protected static ?string $navigationGroup = 'CMS';

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected CustomFieldsManager $customFieldsManager;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {

        return $form
            ->schema(fn (FormCompiler $compiler): array => [
                Split::make(
                    schema: [
                        $compiler->compile(),
                        Group::make()
                            ->schema(
                                components: [
                                    Section::make()
                                        ->schema(
                                            components: [
                                                Select::make('template')
                                                    ->label(__('Template'))
                                                    ->required()
                                                    ->options(function (ThemeManager $themeManager) {
                                                        return $themeManager->getCurrentThemeTemplates();
                                                    }),

                                                Select::make('status')
                                                    ->label(__('Content Status'))
                                                    ->required()
                                                    ->options(options: ContentStatus::class)
                                                    ->searchable()
                                                    ->preload(),

                                                TextInput::make('sort_order')
                                                    ->label(__('Order'))
                                                    ->numeric()
                                                    ->default(1),

                                                DateTimePicker::make('publish_at')
                                                    ->default(now()),
                                            ]
                                        ),
                                    Section::make(__('Featured Image'))
                                        ->label('')
                                        ->schema(
                                            components: [
                                                CuratorPicker::make('featured_image_id')
                                                    ->label(__('Featured Image'))
                                                    ->buttonLabel(__('Add Feature Image'))
                                                    ->size('lg')->listDisplay(true),
                                            ]
                                        ),
                                ])->grow(false),
                    ]
                )->from('lg'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('path'),
                TextColumn::make('status')
                    ->label(__('Status '))
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
