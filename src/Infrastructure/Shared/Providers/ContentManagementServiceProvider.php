<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Shared\Providers;

use Filament\Tables\Actions\CreateAction;
use Illuminate\Support\ServiceProvider;
use WordSphere\Core\Domain\ContentManagement\Repositories\AuthorRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Repositories\MediaRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Repositories\PageRepositoryInterface;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentAuthorRepository;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentContentRepository;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentMediaRepository;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentPageRepository;

class ContentManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {

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
    }
}
