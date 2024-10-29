<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Shared\Providers;

use Filament\Tables\Actions\CreateAction;
use Illuminate\Support\ServiceProvider;
use WordSphere\Core\Application\ContentManagement\Services\CachedContentQueryService;
use WordSphere\Core\Application\ContentManagement\Services\ContentQueryService;
use WordSphere\Core\Domain\ContentManagement\Repositories\AuthorRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Repositories\MediaRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Repositories\PageRepositoryInterface;
use WordSphere\Core\Infrastructure\ContentManagement\Cache\ContentCacheManager;
use WordSphere\Core\Infrastructure\ContentManagement\Observers\ContentObserver;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentAuthorRepository;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentContentRepository;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentMediaRepository;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentPageRepository;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;

class ContentServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        $this->app->bind(ContentQueryService::class, function ($app) {
            return new ContentQueryService(
                tenantProjectProvider: $app->make(TenantProjectProvider::class)
            );
        });

        $this->app->bind(CachedContentQueryService::class, function ($app) {
            return new CachedContentQueryService(
                contentQueryService: $app->make(ContentQueryService::class),
                tenantProjectProvider: $app->make(TenantProjectProvider::class),
                cacheManager: $app->make(ContentCacheManager::class),
                cache: $app->make('cache.store')
            );
        });

        $this->app->bind(
            abstract: MediaRepositoryInterface::class,
            concrete: EloquentMediaRepository::class,
        );

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

    public function boot(): void
    {
        CreateAction::configureUsing(function (CreateAction $action) {
            return $action->slideOver();
        });

        ContentModel::observe(ContentObserver::class);
    }
}
