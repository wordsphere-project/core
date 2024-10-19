<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Listeners;

use Illuminate\Log\LogManager;
use WordSphere\Core\Domain\ContentManagement\Events\ArticlePublished;

readonly class HandleArticlePublished
{

    public function __construct(
        private LogManager $log
    ) {

    }

    public function handle(ArticlePublished $event): void
    {
        $this->log->info("Article published: {$event->articleId}");
    }

}
