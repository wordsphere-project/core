<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Queries;

use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;

readonly class GetPageBySlugQuery
{
    public function __construct(
        public Slug $slug
    ) {}
}
