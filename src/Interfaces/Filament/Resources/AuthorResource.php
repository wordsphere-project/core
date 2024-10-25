<?php

namespace WordSphere\Core\Interfaces\Filament\Resources;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentAuthor;
use WordSphere\Core\Interfaces\Filament\Resources\AuthorResource\Pages\CreateAuthor;
use WordSphere\Core\Interfaces\Filament\Resources\AuthorResource\Pages\EditAuthor;
use WordSphere\Core\Interfaces\Filament\Resources\AuthorResource\Pages\ListAuthors;

use function __;

class AuthorResource extends Resource
{
    protected static ?string $model = EloquentAuthor::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'CMS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                components: [
                    Tabs::make('Tabs')
                        ->tabs([
                            Tab::make('General')
                                ->schema([
                                    FileUpload::make('photo')
                                        ->image()
                                        ->avatar()
                                        ->directory('author-photos')
                                        ->maxSize(1024)
                                        ->disk('public'),
                                    TextInput::make('name')
                                        ->label(__('Name'))
                                        ->minLength(3)
                                        ->required()
                                        ->columnSpan(2),
                                    RichEditor::make('bio')
                                        ->label(__('Bio'))
                                        ->columnSpan(2),
                                    TextInput::make('email')
                                        ->label(__('Email'))
                                        ->prefixIcon('heroicon-o-envelope')
                                        ->email(),
                                    TextInput::make('website')
                                        ->label(__('Website'))
                                        ->prefixIcon('heroicon-o-globe-alt')
                                        ->url(),
                                    Fieldset::make(__('Social Profiles'))
                                        ->statePath('social_links')
                                        ->schema([
                                            TextInput::make('twitter')
                                                ->label(__('Twitter'))
                                                ->prefixIcon('bi-twitter-x')
                                                ->maxLength(255),
                                            TextInput::make('linkedin')
                                                ->label(__('LinkedIn'))
                                                ->prefixIcon('bi-linkedin')
                                                ->maxLength(255),
                                            TextInput::make('facebook')
                                                ->label(__('Facebook'))
                                                ->prefixIcon('bi-facebook')
                                                ->maxLength(255),
                                            TextInput::make('instagram')
                                                ->label(__('Instagram'))
                                                ->prefixIcon('bi-instagram')
                                                ->maxLength(255),
                                            TextInput::make('github')
                                                ->label(__('Github'))
                                                ->prefixIcon('bi-github')
                                                ->maxLength(255),
                                        ]),
                                ])
                                ->columns(2),
                        ]),
                ]
            )->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label(__('Photo'))
                    ->circular(),
                TextColumn::make('name')
                    ->extraAttributes([
                        'class' => 'font-semibold',
                    ])
                    ->searchable()
                    ->label(__('Name')),
                TextColumn::make('website')
                    ->label(__('Website')),

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
            'index' => ListAuthors::route('/'),
            'create' => CreateAuthor::route('/create'),
            'edit' => EditAuthor::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('Authors');
    }

    public static function getLabel(): ?string
    {
        return __('Author');
    }

    public static function getPluralLabel(): string
    {
        return __('Authors');
    }
}
