<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Repositories;

use WordSphere\Core\Domain\ContentManagement\Entities\Page;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;

interface PageRepositoryInterface
{
    public function findByPath(string $path): ?Page;

    public function findBySlug(Slug $slug): ?Page;
}
