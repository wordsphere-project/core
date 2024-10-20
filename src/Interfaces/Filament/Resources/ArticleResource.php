<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Resources;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
use WordSphere\Core\Application\ContentManagement\Commands\PublishArticleCommand;
use WordSphere\Core\Application\ContentManagement\Services\PublishArticleService;
use WordSphere\Core\Domain\ContentManagement\Exceptions\InvalidArticleStatusException;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article as EloquentArticle;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Interfaces\Filament\Resources\ArticleResource\Pages\CreateArticle;
use WordSphere\Core\Interfaces\Filament\Resources\ArticleResource\Pages\EditArticle;
use WordSphere\Core\Interfaces\Filament\Resources\ArticleResource\Pages\ListArticles;

class ArticleResource extends Resource
{
    protected static ?string $model = EloquentArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'CMS';

    public static function form(Form $form): Form
    {
        return $form
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
                    CuratorPicker::make('featured_image_id')
                        ->label(__('Featured Image')),
                ]
            );
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
                    ->action(fn (AuthManager $auth, PublishArticleService $publishArticleService, EloquentArticle $record) => static::publishArticle($auth, $record, $publishArticleService))
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
