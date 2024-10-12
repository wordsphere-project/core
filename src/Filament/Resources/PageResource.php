<?php

namespace WordSphere\Core\Filament\Resources;


use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use WordSphere\Core\Enums\ContentStatus;
use WordSphere\Core\Filament\Resources\PageResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use WordSphere\Core\Models\Page;
use WordSphere\Core\Support\Theme\ThemeManager;
use function __;
use function now;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationGroup = 'CMS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                components: [
                    Split::make(
                        schema: [
                            Section::make()
                                ->schema(
                                    components: [
                                        TextInput::make('title')
                                            ->maxLength(255)
                                            ->required(),

                                        TextInput::make('path')
                                            ->required()
                                            ->unique(
                                                table: 'pages',
                                                column: 'path',
                                                ignoreRecord: true
                                            ),

                                        Textarea::make('excerpt')
                                            ->rows(5),

                                    ]
                                )->grow(true),
                            Section::make()
                                ->schema(
                                    components: [
                                        Select::make('template')
                                            ->label(__('Template'))
                                            ->options(function (ThemeManager $themeManager) {
                                                return $themeManager->getCurrentThemeTemplates();
                                            }),

                                        Select::make('status')
                                            ->label(__('Content Status'))
                                            ->options(options: ContentStatus::class)
                                            ->searchable()
                                            ->preload(),

                                        TextInput::make('sort_order')
                                            ->label(__('Order'))
                                            ->numeric()
                                            ->default(1),

                                        DateTimePicker::make('publish_at')
                                            ->default(now()),
                                    ])->grow(false),
                        ]
                    )->from('lg'),

                ]
            )
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('path'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status '))
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
