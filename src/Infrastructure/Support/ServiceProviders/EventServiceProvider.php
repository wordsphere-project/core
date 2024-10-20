<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Support\ServiceProviders;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use WordSphere\Core\Application\ContentManagement\Listeners\HandleArticlePublished;
use WordSphere\Core\Domain\ContentManagement\Events\ArticlePublished;
use WordSphere\Core\Domain\Shared\Contracts\EventDispatcherInterface;
use WordSphere\Core\Infrastructure\Support\Events\LaravelEventDispatcher;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ArticlePublished::class => [
            HandleArticlePublished::class,
        ],
    ];

    public function register(): void
    {
        $this->app->bind(
            abstract: EventDispatcherInterface::class,
            concrete: LaravelEventDispatcher::class
        );
    }
}
