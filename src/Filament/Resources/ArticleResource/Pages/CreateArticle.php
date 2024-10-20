<?php

declare(strict_types=1);

namespace WordSphere\Core\Filament\Resources\ArticleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Auth\AuthManager;
use WordSphere\Core\Application\ContentManagement\Commands\CreateArticleCommand;
use WordSphere\Core\Application\ContentManagement\Services\CreateArticleService;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\MediaManagement\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Filament\Resources\ArticleResource;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\ArticleAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected CreateArticleService $createArticleService;

    protected ArticleRepositoryInterface $articleRepository;

    protected AuthManager $auth;

    public function boot(
        CreateArticleService $createArticleService,
        ArticleRepositoryInterface $articleRepository,
        AuthManager $auth
    ): void {
        $this->createArticleService = $createArticleService;
        $this->articleRepository = $articleRepository;
        $this->auth = $auth;
    }

    public function handleRecordCreation(array $data): Article
    {
        /** @var EloquentUser $user */
        $user = $this->auth->user();

        $command = new CreateArticleCommand(
            createdBy: Uuid::fromString($user->uuid),
            title: $data['title'],
            content: $data['content'] ?? null,
            excerpt: $data['excerpt'] ?? null,
            slug: $data['slug'] ?? null,
            customFields: $data['data'] ?? null,
            featuredImage: $data['featured_image_id'] ? Id::fromInt($data['featured_image_id']) : null,
        );

        $articleUuid = $this->createArticleService->execute($command);

        $domainArticle = $this->articleRepository->findByUuid($articleUuid);

        return ArticleAdapter::toEloquent(
            domainArticle: $domainArticle,
        );

    }
}
