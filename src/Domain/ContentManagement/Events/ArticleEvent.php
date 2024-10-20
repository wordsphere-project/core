<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Events;

use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleUuid;

class ArticleEvent
{
    public function __construct(
        public ArticleUuid $articleId,
    ) {}
}
