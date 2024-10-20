<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Support\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Repositories\AuthorRepositoryInterface;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentArticleRepository;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentAuthorRepository;

final class ContentManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            abstract: ArticleRepositoryInterface::class,
            concrete: EloquentArticleRepository::class,
        );

        $this->app->bind(
            abstract: AuthorRepositoryInterface::class,
            concrete: EloquentAuthorRepository::class,
        );

    }
}
