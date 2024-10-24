<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Resources;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Auth\AuthManager;
use WordSphere\Core\Application\ContentManagement\Commands\PublishArticleCommand;
use WordSphere\Core\Application\ContentManagement\Services\ContentStatusServiceFactory;
use WordSphere\Core\Application\ContentManagement\Services\PublishArticleService;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\Enums\ArticleStatus;
use WordSphere\Core\Domain\ContentManagement\Exceptions\InvalidArticleStatusException;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article as EloquentArticle;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Interfaces\Filament\Concerns\InteractsWithStatus;
use WordSphere\Core\Interfaces\Filament\Resources\ArticleResource\Pages\CreateArticle;
use WordSphere\Core\Interfaces\Filament\Resources\ArticleResource\Pages\EditArticle;
use WordSphere\Core\Interfaces\Filament\Resources\ArticleResource\Pages\ListArticles;

use function __;

class ArticleResource extends Resource
{
    use InteractsWithStatus;

    protected static ?string $model = EloquentArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'CMS';

    public static function getContentType(): string
    {
        return Content::class;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                components: [
                    Split::make([
                        Tabs::make('Main Content')
                            ->tabs([
                                Tab::make('General')
                                    ->label(__('General'))
                                    ->schema(
                                        components: [
                                            TextInput::make('title')
                                                ->columnSpan(2)
                                                ->required(),
                                            TextInput::make('slug')
                                                ->columnSpan(2)
                                                ->required(),
                                            Textarea::make('excerpt')
                                                ->columnSpan(2),
                                            RichEditor::make('content')
                                                ->columnSpan(2),
                                        ]
                                    ),
                            ]),

                        Group::make()
                            ->schema([
                                Section::make(__('Publish'))
                                    ->schema(
                                        components: [
                                            Forms\Components\Placeholder::make('status')
                                                ->label(__('status.label'))
                                                ->content(fn (EloquentArticle $record) => $record->status)
                                                ->visible(fn (): bool => $form->getRecord() !== null),
                                            Select::make('visibility')
                                                ->options([
                                                    'public' => 'Public',
                                                    'private' => 'Private',
                                                ])
                                                ->label('Visibility'),
                                            DateTimePicker::make('published_at')
                                                ->label('Publish at')
                                                ->reactive(),
                                            Actions::make([
                                                Forms\Components\Actions\Action::make('publish')
                                                    ->label('Publish')
                                                    ->action(function (EloquentArticle $record, ContentStatusServiceFactory $serviceFactory, AuthManager $authManager, Get $get, Set $set): void {
                                                        static::publishContent($record, $serviceFactory, $authManager, $get, $set);
                                                    })
                                                    ->visible(fn (?EloquentArticle $record): bool => $record?->status === ArticleStatus::DRAFT->toString()
                                                    )
                                                    ->keyBindings(['command+p', 'ctrl+p'])
                                                    ->color('success'),

                                                Forms\Components\Actions\Action::make('unpublish')
                                                    ->label('Unpublish')
                                                    ->action(function (EloquentArticle $record, ContentStatusServiceFactory $serviceFactory, AuthManager $authManager, Get $get, Set $set): void {
                                                        static::unpublishContent($record, $serviceFactory, $authManager, $get, $set);
                                                    })
                                                    ->visible(fn (?EloquentArticle $record): bool => $record?->status === ArticleStatus::PUBLISHED->toString()
                                                    )
                                                    ->keyBindings(['command+u', 'ctrl+u'])
                                                    ->color('danger'),
                                            ]),
                                            Select::make('author_id')
                                                ->relationship('author', 'name')
                                                ->searchable(),
                                        ]
                                    )
                                    ->collapsible(),
                                Section::make()
                                    ->heading(__('Featured image'))
                                    ->schema(
                                        components: [
                                            CuratorPicker::make('featured_image_id')
                                                ->hiddenLabel()
                                                ->extraAttributes(['class' => 'max-w-xs']),
                                        ]
                                    ),

                            ])
                            ->grow(false)
                            ->columns(['md' => 2, 'lg' => 1])
                            ->extraAttributes(['class' => 'min-w-80 lg:max-w-xs']),

                    ])
                        ->columns(1)
                        ->from('lg'),

                ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                components: [
                    CuratorColumn::make('featured_image_id')
                        ->circular()
                        ->label('Feature Image')
                        ->size(60),
                    TextColumn::make('title')
                        ->searchable(),
                    TextColumn::make('slug')
                        ->searchable(),
                    TextColumn::make('status')
                        ->badge(),
                ]
            )
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('publish')
                    ->label(__('Publish'))
                    ->action(function (EloquentArticle $record, ContentStatusServiceFactory $serviceFactory, AuthManager $authManager): void {
                        static::publishContent($record, $serviceFactory, $authManager);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (EloquentArticle $record): bool => $record->status !== 'published'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function publishArticle(AuthManager $auth, EloquentArticle $record, PublishArticleService $publishArticleService): void
    {

        /** @var EloquentUser $user */
        $user = $auth->user();

        $publisher = Uuid::fromString($user->uuid);
        $command = new PublishArticleCommand($record->id, $publisher);

        try {
            $publishArticleService->execute($command);
        } catch (InvalidArticleStatusException $exception) {
            Notification::make()
                ->title(__('Unable to publish article.'))
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
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
            'index' => ListArticles::route('/'),
            'create' => CreateArticle::route('/create'),
            'edit' => EditArticle::route('/{record}/edit'),
        ];
    }
}
