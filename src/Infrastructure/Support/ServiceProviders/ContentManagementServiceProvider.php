<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Support\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Repositories\AuthorRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Repositories\PageRepositoryInterface;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentArticleRepository;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentAuthorRepository;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentPageRepository;

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

        $this->app->bind(
            abstract: PageRepositoryInterface::class,
            concrete: EloquentPageRepository::class,
        );

    }
}
