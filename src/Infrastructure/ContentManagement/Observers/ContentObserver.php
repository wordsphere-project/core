<?php

namespace WordSphere\Core\Infrastructure\ContentManagement\Observers;

use WordSphere\Core\Infrastructure\ContentManagement\Cache\ContentCacheManager;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;

readonly class ContentObserver
{
    public function __construct(
        private ContentCacheManager $cacheManager,
    ) {}

    public function saved(ContentModel $content): void
    {
        $this->cacheManager->invalidateContent($content);
    }

    public function deleted(ContentModel $content): void
    {
        $this->cacheManager->invalidateContent($content);
    }

    public function restored(ContentModel $content): void
    {
        $this->cacheManager->invalidateContent($content);
    }
}
