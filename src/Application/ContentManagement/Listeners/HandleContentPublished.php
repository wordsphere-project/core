<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Listeners;

use Illuminate\Log\LogManager;
use WordSphere\Core\Domain\ContentManagement\Events\ContentPublished;

readonly class HandleContentPublished
{
    public function __construct(
        private LogManager $log
    ) {}

    public function handle(ContentPublished $event): void
    {
        $this->log->info("EloquentContent published: {$event->articleId}");
    }
}
