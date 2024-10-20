<?php

declare(strict_types=1);

namespace WordSphere\Core\Filament\Resources\ArticleResource\Pages;

use Exception;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Application\ContentManagement\Commands\UpdateArticleCommand;
use WordSphere\Core\Application\ContentManagement\Services\UpdateArticleService;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleUuid;
use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;
use WordSphere\Core\Filament\Resources\ArticleResource;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\ArticleAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article as EloquentArticle;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected UpdateArticleService $updateArticleService;

    protected ArticleRepositoryInterface $articleRepository;

    protected AuthManager $auth;

    public function boot(
        UpdateArticleService $updateArticleService,
        ArticleRepositoryInterface $articleRepository,
        AuthManager $auth
    ): void {
        $this->updateArticleService = $updateArticleService;
        $this->articleRepository = $articleRepository;
        $this->auth = $auth;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(EloquentArticle|Model $record, array $data): EloquentArticle
    {

        if (! $record instanceof EloquentArticle) {
            throw new Exception(__('Article not found.'));
        }

        /** @var EloquentUser $updater */
        $updater = $this->auth->user();
        $updaterId = UserUuid::fromString($updater->uuid);
        $articleUuid = ArticleUuid::fromString($record->id);

        $command = new UpdateArticleCommand(
            id: $articleUuid,
            updater: $updaterId,
            title: $data['title'] ?? null,
            content: array_key_exists('content', $data) ? $data['content'] : null,
            excerpt: array_key_exists('excerpt', $data) ? $data['excerpt'] : null,
            slug: array_key_exists('slug', $data) ? $data['slug'] : null,
            customFields: array_key_exists('data', $data) ? $data['data'] : null
        );

        $this->updateArticleService->execute($command);

        $updatedDomainArticle = $this->articleRepository->findByUuid($articleUuid);

        return ArticleAdapter::toEloquent($updatedDomainArticle);
    }

    protected function getUpdatedValue(array $data, EloquentArticle $record, string $field, $default = null)
    {
        return array_key_exists($field, $data) ? $data[$field] : $record->{$field};
    }
}
