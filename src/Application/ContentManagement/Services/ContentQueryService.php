<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use WordSphere\Core\Application\ContentManagement\Queries\GetContentBySlugQuery;
use WordSphere\Core\Application\ContentManagement\Queries\GetContentsByTypeQuery;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;

readonly class ContentQueryService
{
    public function __construct(
        private TenantProjectProvider $tenantProjectProvider
    ) {}

    public function getContentsByType(GetContentsByTypeQuery $query): LengthAwarePaginator
    {
        return ContentModel::query()
            ->where('type', $query->type->toString())
            ->where('tenant_id', $this->tenantProjectProvider->getCurrentTenantId()->toString())
            ->where('project_id', $this->tenantProjectProvider->getCurrentProjectId()->toString())
            ->orderBy($query->orderBy, $query->orderDirection)
            ->paginate(
                perPage: $query->perPage,
                page: $query->page
            );
    }

    public function getContentBySlug(GetContentBySlugQuery $query): ?ContentModel
    {
        return ContentModel::query()
            ->where('type', $query->type->toString())
            ->where('slug', $query->slug)
            ->where('tenant_id', $this->tenantProjectProvider->getCurrentTenantId()->toString())
            ->where('project_id', $this->tenantProjectProvider->getCurrentProjectId()->toString())
            ->first();
    }
}
