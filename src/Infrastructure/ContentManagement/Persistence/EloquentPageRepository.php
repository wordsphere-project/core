<?php

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence;

use WordSphere\Core\Domain\ContentManagement\Entities\Page;
use WordSphere\Core\Domain\ContentManagement\Repositories\PageRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\PageAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentPage;

class EloquentPageRepository implements PageRepositoryInterface
{
    public function findByPath(string $path): ?Page
    {
        $eloquentPage = EloquentPage::query()
            ->where('path', $path)
            ->first();

        return $eloquentPage ? PageAdapter::toDomain($eloquentPage) : null;

    }

    public function findBySlug(Slug $slug): ?Page
    {
        $eloquentPage = EloquentPage::query()
            ->where('slug', $slug)
            ->first();

        return $eloquentPage ? PageAdapter::toDomain($eloquentPage) : null;
    }
}
