<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Resources;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Forms\Form;
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
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\Exceptions\InvalidContentStatusException;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel as EloquentArticle;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Interfaces\Filament\Builders\TypeNavigationBuilder;
use WordSphere\Core\Interfaces\Filament\Concerns\InteractsWithStatus;
use WordSphere\Core\Interfaces\Filament\Concerns\InteractsWithType;
use WordSphere\Core\Interfaces\Filament\Resolvers\TypeRouteResolver;
use WordSphere\Core\Interfaces\Filament\Resources\ContentResource\ContentForm;
use WordSphere\Core\Interfaces\Filament\Resources\ContentResource\Pages\CreateContent;
use WordSphere\Core\Interfaces\Filament\Resources\ContentResource\Pages\EditContent;
use WordSphere\Core\Interfaces\Filament\Resources\ContentResource\Pages\ListContents;

use function __;
use function app;
use function request;

class ContentResource extends Resource
{
    use InteractsWithStatus;
    use InteractsWithType;

    protected static ?string $model = EloquentArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'CMS';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $slug = 'contents';

    protected static ?string $panel = 'wordsphere';

    public static function getEntityClass(): string
    {
        return Content::class;
    }

    public static function form(Form $form): Form
    {

        return $form
            ->schema(
                ContentForm::make($form, self::getType())
            )
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('slug')
            ->columns(
                components: [
                    CuratorColumn::make('featured_image_id')
                        ->circular()
                        ->label('Feature Image')
                        ->visible(fn () => ! self::getType()->getKey()->equals(TypeKey::fromString('blocks')))
                        ->size(60),
                    TextColumn::make('title')
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('slug')
                        ->sortable()
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

    public static function getNavigationItems(): array
    {
        $navigationBuilder = app(TypeNavigationBuilder::class);

        return $navigationBuilder->build();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContents::route('/{type}'),
            'create' => CreateContent::route('/{type}/create'),
            'edit' => EditContent::route('/{type}/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $type = app(TypeRouteResolver::class)->resolve(request());

        return parent::getEloquentQuery()
            ->where('type', $type);
    }

    public static function getRelations(): array
    {

        $relations = [];

        if (request()->route()) {
            $type = self::getType();
        }

        return $relations;

    }
}
