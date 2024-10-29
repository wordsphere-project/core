<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Queries;

use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;

class GetContentsByTypeQuery
{
    public function __construct(
        public readonly TypeKey $type,
        public readonly int $page = 1,
        public readonly int $perPage = 10,
        public readonly string $orderBy = 'created_at',
        public readonly string $orderDirection = 'desc'
    ) {}
}
