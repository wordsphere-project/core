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
use Illuminate\Database\Eloquent\Builder;
use WordSphere\Core\Application\ContentManagement\Commands\PublishContentCommand;
use WordSphere\Core\Application\ContentManagement\Services\ContentStatusServiceFactory;
use WordSphere\Core\Application\ContentManagement\Services\PublishContentService;
use WordSphere\Core\Domain\ContentManagement\ContentTypeRegistry;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\Enums\ContentStatus;
use WordSphere\Core\Domain\ContentManagement\Exceptions\InvalidContentStatusException;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentContent as EloquentArticle;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Interfaces\Filament\Concerns\InteractsWithContentType;
use WordSphere\Core\Interfaces\Filament\Concerns\InteractsWithStatus;
use WordSphere\Core\Interfaces\Filament\Hooks\ContentTypeFieldsHook;
use WordSphere\Core\Interfaces\Filament\Resolvers\ContentTypeRouteResolver;
use WordSphere\Core\Interfaces\Filament\Resources\ContentResource\Pages\CreateContent;
use WordSphere\Core\Interfaces\Filament\Resources\ContentResource\Pages\EditContent;
use WordSphere\Core\Interfaces\Filament\Resources\ContentResource\Pages\ListContents;

use function __;
use function app;
use function request;

class ContentResource extends Resource
{
    use InteractsWithContentType;
    use InteractsWithStatus;

    protected static ?string $model = EloquentArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'CMS';

    protected static bool $shouldRegisterNavigation = true;

    public static function getEntityClass(): string
    {
        return Content::class;
    }

    public static function form(Form $form): Form
    {

        /** @var string $contentType */
        $contentType = app(ContentTypeRouteResolver::class)->resolve(request());

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
                                            Forms\Components\Hidden::make('type')
                                                ->default($contentType),
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

                                Tab::make('Media')
                                    ->schema([
                                        CuratorPicker::make('media_ids')
                                            ->label('Media Gallery')
                                            ->multiple()
                                            ->relationship('media', 'id')
                                            ->buttonLabel('Add Media')
                                            ->columnSpanFull()
                                            ->acceptedFileTypes(['image/*'])
                                            ->helperText('Add images to the media gallery')
                                            ->orderColumn('order')
                                            ->reactive(),
                                    ]),
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
                                                    ->visible(fn (?EloquentArticle $record): bool => $record?->status === ContentStatus::DRAFT->toString()
                                                    )
                                                    ->keyBindings(['command+p', 'ctrl+p'])
                                                    ->color('success'),

                                                Forms\Components\Actions\Action::make('unpublish')
                                                    ->label('Unpublish')
                                                    ->action(function (EloquentArticle $record, ContentStatusServiceFactory $serviceFactory, AuthManager $authManager, Get $get, Set $set): void {
                                                        static::unpublishContent($record, $serviceFactory, $authManager, $get, $set);
                                                    })
                                                    ->visible(fn (?EloquentArticle $record): bool => $record?->status === ContentStatus::PUBLISHED->toString()
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

    public static function publishArticle(AuthManager $auth, EloquentArticle $record, PublishContentService $publishArticleService): void
    {

        /** @var EloquentUser $user */
        $user = $auth->user();

        $publisher = Uuid::fromString($user->uuid);
        $command = new PublishContentCommand($record->id, $publisher);

        try {
            $publishArticleService->execute($command);
        } catch (InvalidContentStatusException $exception) {
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

    public static function getNavigationItems(): array
    {
        $registry = app(ContentTypeRegistry::class);
        $items = [];

        foreach ($registry->all() as $contentType) {
            $items[] = \Filament\Navigation\NavigationItem::make()
                ->label($contentType->pluralName)
                ->icon($contentType->icon)
                ->group($contentType->navigationGroup)
                // Direct path construction is more reliable in this case
                ->url("/admin/contents/{$contentType->key}");
        }

        return $items;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContents::route('/{contentType}'),
            'create' => CreateContent::route('/{contentType}/create'),
            'edit' => EditContent::route('/{contentType}/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $contentType = app(ContentTypeRouteResolver::class)->resolve(request());

        return parent::getEloquentQuery()
            ->where('type', $contentType);
    }

    protected static function getCustomFields(string $contentType, string $location): array
    {
        return app(ContentTypeFieldsHook::class)->getCustomFields($contentType, $location);
    }

    protected static function getCustomTabs(string $contentType): array
    {
        $fieldsHook = app(ContentTypeFieldsHook::class);
        $tabs = [];
        foreach ($fieldsHook->getAvailableLocations($contentType) as $location) {
            if (str_starts_with($location, 'tab.')) {
                $tabName = substr($location, 4);
                $tabs[] = Forms\Components\Tabs\Tab::make($tabName)
                    ->schema($fieldsHook->getCustomFields($contentType, $location));
            }
        }

        return $tabs;
    }
}
