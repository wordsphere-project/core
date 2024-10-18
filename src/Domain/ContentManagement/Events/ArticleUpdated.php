<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Events;

use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;

class ArticleUpdated
{
    public function __construct(
        public ArticleId $articleId,
    ) {}
}
