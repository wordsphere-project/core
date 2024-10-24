<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Support\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use WordSphere\Core\Domain\ContentManagement\Repositories\AuthorRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Repositories\PageRepositoryInterface;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentAuthorRepository;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentContentRepository;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentPageRepository;

final class ContentManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            abstract: ContentRepositoryInterface::class,
            concrete: EloquentContentRepository::class,
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
