<?php

declare(strict_types=1);

namespace WordSphere\Core\Filament\Resources\ArticleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use WordSphere\Core\Application\ContentManagement\Commands\CreateArticleCommand;
use WordSphere\Core\Application\ContentManagement\Services\CreateArticleService;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Filament\Resources\ArticleResource;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\ArticleAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article;


class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected CreateArticleService $createArticleService;
    protected ArticleRepositoryInterface $articleRepository;

    public function boot(
        CreateArticleService $createArticleService,
        ArticleRepositoryInterface $articleRepository,
    ): void {
        $this->createArticleService = $createArticleService;
        $this->articleRepository = $articleRepository;
    }

    public function handleRecordCreation(array $data): Article
    {

        $command = new CreateArticleCommand(
            title: $data['title'],
            content: $data['content'] ?? null,
            excerpt: $data['excerpt'] ?? null,
            slug: $data['slug'] ?? null,
            data: $data['data'] ?? null,
        );

        $articleId = $this->createArticleService->execute($command);

        $domainArticle = $this->articleRepository->findById($articleId);

        return ArticleAdapter::toEloquent(
            domainArticle: $domainArticle,
        );

    }
}
