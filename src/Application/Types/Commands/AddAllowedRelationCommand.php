<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\Types\Commands;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

readonly class AddAllowedRelationCommand
{
    public function __construct(
        public Uuid $sourceTypeId,
        public string $name,
        public Uuid $targetTypeId,
        public string $relationType,
        public bool $isRequired = false,
        public ?int $minItems = null,
        public ?int $maxItems = null,
        public ?string $inverseRelationName = null,
    ) {}
}
