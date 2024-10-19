<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ServiceProviders;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use WordSphere\Core\Application\ContentManagement\Listeners\HandleArticlePublished;
use WordSphere\Core\Domain\ContentManagement\Events\ArticlePublished;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ArticlePublished::class => [
            HandleArticlePublished::class,
        ],
    ];
}
