<?php

declare(strict_types=1);

namespace WordSphere\Core\Filament\Resources\ArticleResource\Pages;

use Exception;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Application\ContentManagement\Commands\UpdateArticleCommand;
use WordSphere\Core\Application\ContentManagement\Services\UpdateArticleService;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;
use WordSphere\Core\Filament\Resources\ArticleResource;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\ArticleAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article;
use function dump;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected UpdateArticleService $updateArticleService;
    protected ArticleRepositoryInterface $articleRepository;

    public function boot(
        UpdateArticleService $updateArticleService,
        ArticleRepositoryInterface $articleRepository
    ): void {
        $this->updateArticleService = $updateArticleService;
        $this->articleRepository = $articleRepository;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Article|Model $record, array $data): Article
    {

        if (!$record instanceof Article) {
            throw new Exception(__('Article not found.'));
        }

        $articleId = ArticleId::fromString($record->id);

        $command = new UpdateArticleCommand(
            id: $articleId,
            title: $data['title'] ?? null,
            content: array_key_exists('content', $data) ? $data['content'] : null,
            excerpt: array_key_exists('excerpt', $data) ? $data['excerpt'] : null,
            slug: array_key_exists('slug', $data) ? $data['slug'] : null,
            data: array_key_exists('data', $data) ? $data['data'] : null
        );

        $this->updateArticleService->execute($command);

        $updatedDomainArticle = $this->articleRepository->findById($articleId);

        return ArticleAdapter::toEloquent($updatedDomainArticle);
    }

    protected function getUpdatedValue(array $data, Article $record, string $field, $default = null)
    {
        return array_key_exists($field, $data) ? $data[$field] : $record->{$field};
    }
}
