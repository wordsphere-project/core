<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Queries;

use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;

class GetContentBySlugQuery
{
    public function __construct(
        public readonly TypeKey $type,
        public readonly string $slug
    ) {}
}
