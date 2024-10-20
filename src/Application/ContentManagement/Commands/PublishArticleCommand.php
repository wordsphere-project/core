<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Commands;

use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;

readonly class PublishArticleCommand
{
    public function __construct(
        public string $id,
        public UserUuid $publisher
    ) {}
}
