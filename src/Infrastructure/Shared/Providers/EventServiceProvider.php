<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Shared\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use WordSphere\Core\Application\ContentManagement\Listeners\HandleContentPublished;
use WordSphere\Core\Domain\ContentManagement\Events\ContentPublished;
use WordSphere\Core\Domain\Shared\Contracts\EventDispatcherInterface;
use WordSphere\Core\Infrastructure\Shared\Events\LaravelEventDispatcher;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ContentPublished::class => [
            HandleContentPublished::class,
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
