<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\DTOs;

readonly class AuthorStateDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $bio = null,
        public ?string $website = null,
        public ?string $photo = null,
        public ?array $socialLinks = null
    ) {}
}
