<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Clusters\Settings\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use WordSphere\Core\Domain\Types\Enums\RelationType;
use WordSphere\Core\Infrastructure\Types\Persistence\Models\TypeModel;
use WordSphere\Core\Interfaces\Filament\Clusters\Settings;
use WordSphere\Core\Interfaces\Filament\Clusters\Settings\Resources\TypeResource\Pages;

use function __;

class TypeResource extends Resource
{
    protected static ?string $model = TypeModel::class;

    protected static ?string $cluster = Settings::class;

    //protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationLabel = 'Content Types';

    public static function getLabel(): string
    {
        return __('Custom Type');
    }

    public static function getPluralLabel(): string
    {
        return __('Custom Types');
    }

    public static function getNavigationLabel(): string
    {
        return __('Manage Custom Types');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Type Management')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Information')
                            ->schema([
                                Forms\Components\TextInput::make('key')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Unique identifier for this type (lowercase, numbers, hyphens and underscores only)')
                                    ->regex('/^[a-z0-9_-]+$/')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('entity_class')
                                    ->required()
                                    ->helperText('Fully qualified class name that implements TypeableInterface')
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Tabs\Tab::make('Relations')
                            ->schema([
                                Forms\Components\Repeater::make('allowedRelations')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->helperText('e.g., "category", "related_posts"')
                                                    ->regex('/^[a-z0-9_]+$/'),

                                                Forms\Components\Select::make('target_type_id')
                                                    ->relationship('targetType', 'key')
                                                    ->required()
                                                    ->searchable(),

                                                Forms\Components\Select::make('relation_type')
                                                    ->options([
                                                        RelationType::ONE_TO_ONE->value => 'One to One',
                                                        RelationType::ONE_TO_MANY->value => 'One to Many',
                                                        RelationType::MANY_TO_MANY->value => 'Many to Many',
                                                        RelationType::BELONGS_TO->value => 'Belongs To',
                                                        RelationType::BELONGS_TO_MANY->value => 'Belongs To Many',
                                                    ])
                                                    ->required()
                                                    ->reactive(),
                                            ]),

                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Toggle::make('is_required')
                                                    ->default(false)
                                                    ->inline(false),

                                                Forms\Components\TextInput::make('min_items')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->visible(fn (callable $get) => in_array($get('relation_type'), [
                                                        RelationType::ONE_TO_MANY->value,
                                                        RelationType::MANY_TO_MANY->value,
                                                        RelationType::BELONGS_TO_MANY->value,
                                                    ])),

                                                Forms\Components\TextInput::make('max_items')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->visible(fn (callable $get) => in_array($get('relation_type'), [
                                                        RelationType::ONE_TO_MANY->value,
                                                        RelationType::MANY_TO_MANY->value,
                                                        RelationType::BELONGS_TO_MANY->value,
                                                    ])),
                                            ]),

                                        Forms\Components\TextInput::make('inverse_relation_name')
                                            ->required(fn (callable $get) => in_array($get('relation_type'), [
                                                RelationType::BELONGS_TO->value,
                                                RelationType::BELONGS_TO_MANY->value,
                                            ]))
                                            ->helperText('Name of the inverse relation on the target type')
                                            ->regex('/^[a-z0-9_]+$/')
                                            ->columnSpanFull(),
                                    ])
                                    ->orderColumn()
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Relation')
                                    ->collapsible()
                                    ->collapseAllAction(
                                        fn (Forms\Components\Actions\Action $action) => $action->label('Collapse All')
                                    )
                                    ->expandAllAction(
                                        fn (Forms\Components\Actions\Action $action) => $action->label('Expand All')
                                    ),
                            ]),

                        Forms\Components\Tabs\Tab::make('Advanced')
                            ->schema([
                                // Add any advanced settings here
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Type key copied')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('entity_class')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('allowedRelations_count')
                    ->label('Relations')
                    ->counts('allowedRelations')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('7xl'),

                Tables\Actions\DeleteAction::make()
                    ->before(function (TypeModel $record) {
                        // Add any pre-deletion checks here
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTypes::route('/'),
            'create' => Pages\CreateType::route('/create'),
            'edit' => Pages\EditType::route('/{record}/edit'),
        ];
    }
}
