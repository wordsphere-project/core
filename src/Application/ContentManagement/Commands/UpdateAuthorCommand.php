<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Commands;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

readonly class UpdateAuthorCommand
{
    public function __construct(
        public Uuid $id,
        public Uuid $updatedBy,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $bio = null,
        public ?string $website = null,
        public ?string $photo = null,
        public ?array $socialLinks = null,
    ) {}
}
