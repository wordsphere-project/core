<?php

namespace WordSphere\Core\Interfaces\Filament\Resources;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Resources\Components\Tab;
use Filament\Tables\Columns\TextColumn;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentAuthor;
use WordSphere\Core\Interfaces\Filament\Resources\AuthorResource\Pages;
use WordSphere\Core\Interfaces\Filament\Resources\AuthorResource\RelationManagers;
use App\Models\Author;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use function __;

class AuthorResource extends Resource
{
    protected static ?string $model = EloquentAuthor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'CMS';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Split::make(
                    schema: [
                        Forms\Components\Tabs::make('Tabs')
                        ->tabs([
                            Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\RichEditor::make('bio')
                                    ->label(__('Bio'))
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('email')
                                    ->label(__('Email'))
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->email(),
                                Forms\Components\TextInput::make('website')
                                    ->label(__('Website'))
                                    ->prefixIcon('heroicon-o-globe-alt')
                                    ->url(),
                                Forms\Components\Fieldset::make(__('Social Profiles'))
                                    ->statePath('socialLinks')
                                    ->schema([
                                        Forms\Components\TextInput::make('twitter')
                                            ->label(__('Twitter'))
                                            ->prefixIcon('bi-twitter-x')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('linkedin')
                                            ->label(__('LinkedIn'))
                                            ->prefixIcon('bi-linkedin')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('facebook')
                                            ->label(__('Facebook'))
                                            ->prefixIcon('bi-facebook')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('instagram')
                                            ->label(__('Instagram'))
                                            ->prefixIcon('bi-instagram')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('github')
                                            ->label(__('Github'))
                                            ->prefixIcon('bi-github')
                                            ->maxLength(255),
                                    ])
                            ])
                            ->columns(2)
                        ]),
                        Forms\Components\Group::make()
                        ->schema(
                            components: [
                                Forms\Components\Section::make()
                                    ->schema([
                                        CuratorPicker::make('featured_image_id')
                                    ])
                            ]
                        )
                            ->grow(false)
                    ]
                )->from('lg')
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('Name')),
                TextColumn::make('website')
                    ->label(__('Website')),

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
            'index' => Pages\ListAuthors::route('/'),
            'create' => Pages\CreateAuthor::route('/create'),
            'edit' => Pages\EditAuthor::route('/{record}/edit'),
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
