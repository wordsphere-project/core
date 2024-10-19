<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use WordSphere\Core\Application\ContentManagement\Services\PublishArticleService;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentArticleRepository;

final class ContentManagementServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(
            abstract: ArticleRepositoryInterface::class,
            concrete: EloquentArticleRepository::class,
        );

    }

}
