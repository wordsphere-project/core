<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\Types\Commands;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;

readonly class CreateTypeCommand
{
    public function __construct(
        public TypeKey $key,
        public string $entityClass,
        public ?Uuid $tenantId = null,
        public ?Uuid $projectId = null,
    ) {}
}
