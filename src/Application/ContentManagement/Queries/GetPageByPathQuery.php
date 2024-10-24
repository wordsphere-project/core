<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Queries;

readonly class GetPageByPathQuery
{
    public function __construct(
        public string $path
    ) {}
}
