<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Commands;

readonly class PublishArticleCommand
{
    public function __construct(
        public string $id,
    ) {}
}
