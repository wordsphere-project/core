<?php

declare(strict_types=1);

namespace WordSphere\Core\Filament\Resources;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use WordSphere\Core\Application\ContentManagement\Commands\PublishArticleCommand;
use WordSphere\Core\Application\ContentManagement\Services\PublishArticleService;
use WordSphere\Core\Domain\ContentManagement\Exceptions\InvalidArticleStatusException;
use WordSphere\Core\Filament\Resources\ArticleResource\Pages;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article as EloquentArticle;

class ArticleResource extends Resource
{
    protected static ?string $model = EloquentArticle::class;

    protected static ?string $navigationGroup = 'CMS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                components: [
                    Forms\Components\TextInput::make('title')
                        ->columnSpan(2)
                        ->required(),
                    Forms\Components\TextInput::make('slug')
                        ->columnSpan(2)
                        ->required(),
                    Forms\Components\Textarea::make('excerpt')
                        ->columnSpan(2),
                    Forms\Components\RichEditor::make('content')
                        ->columnSpan(2),
                    CuratorPicker::make('media_id'),
                ]
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                components: [
                    CuratorColumn::make('media_id')
                        ->label('Feature Image')
                        ->size(40),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('publish')
                    ->label(__('Publish'))
                    ->action(fn (PublishArticleService $publishArticleService, EloquentArticle $record) => static::publishArticle($record, $publishArticleService))
                    ->requiresConfirmation()
                    ->visible(fn (EloquentArticle $record): bool => $record->status !== 'published'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function publishArticle(EloquentArticle $record, PublishArticleService $publishArticleService): void
    {
        $command = new PublishArticleCommand($record->id);

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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
