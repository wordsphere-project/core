<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Events;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

class ArticleEvent
{
    public function __construct(
        public Uuid $articleId,
    ) {}
}
