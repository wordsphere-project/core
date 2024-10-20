<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Commands;

use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

readonly class CreateAuthorCommand
{
    public function __construct(
        public string $name,
        public Uuid $createdBy,
        public ?string $email = null,
        public ?string $bio = null,
        public ?string $website = null,
        public ?array $socialLinks = [],
        public ?Id $featuredImage = null,
    ) {}
}
