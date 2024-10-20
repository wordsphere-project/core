<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Events;

use WordSphere\Core\Domain\ContentManagement\DTOs\AuthorStateDTO;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

readonly class AuthorUpdated
{
    public function __construct(
        public Uuid $authorId,
        public AuthorStateDTO $changes,
    ) {}
}
