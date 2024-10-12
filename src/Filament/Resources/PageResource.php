<?php

namespace WordSphere\Core\Filament\Resources;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\PathGenerators\DatePathGenerator;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use WordSphere\Core\Enums\ContentStatus;
use WordSphere\Core\Filament\Resources\PageResource\Pages;
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
                                            ->label(__('Title'))
                                            ->maxLength(255)
                                            ->columnSpan(2)
                                            ->required(),

                                        TextInput::make('path')
                                            ->label(__('Path'))
                                            ->columnSpan(2)
                                            ->required()
                                            ->unique(
                                                table: 'pages',
                                                column: 'path',
                                                ignoreRecord: true
                                            ),

                                        Textarea::make('excerpt')
                                            ->label(__('Excerpt'))
                                            ->columnSpan(2)
                                            ->visible(fn ($get): bool => $get('excerptSupport'))
                                            ->required()
                                            ->rows(4),

                                        RichEditor::make('content')
                                            ->label(__('Content'))
                                            ->columnSpan(2)
                                            ->visible(fn ($get): bool => $get('contentSupport'))
                                            ->required(),

                                    ]
                                )
                                ->columns(2)
                                ->grow(true),
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
                                                    CuratorPicker::make('media_id')
                                                        ->label(__('Featured Image'))
                                                        ->buttonLabel(__('Add Feature Image'))
                                                        ->pathGenerator(DatePathGenerator::class)
                                                        ->size('lg')->listDisplay(true),
                                                ]
                                            ),
                                        Section::make(__('Settings'))
                                            ->schema(
                                                components: [
                                                    Toggle::make('excerptSupport')
                                                        ->label(__('Excerpt'))
                                                        ->default(false)
                                                        ->dehydrated(false)
                                                        ->reactive(),
                                                    Toggle::make('contentSupport')
                                                        ->label(__('Content'))
                                                        ->default(true)
                                                        ->dehydrated(false)
                                                        ->reactive(),
                                                ]
                                            ),
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
