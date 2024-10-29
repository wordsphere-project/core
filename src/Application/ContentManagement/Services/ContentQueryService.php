<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use WordSphere\Core\Application\ContentManagement\Queries\GetContentBySlugQuery;
use WordSphere\Core\Application\ContentManagement\Queries\GetContentsByTypeQuery;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;
use WordSphere\Core\Interfaces\Http\Api\ContentManagement\Resources\ContentResource;

use function array_merge;

readonly class ContentQueryService
{
    public function __construct(
        private TenantProjectProvider $tenantProjectProvider,
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
        $content = ContentModel::query()
            ->where('type', $query->type->toString())
            ->where('slug', $query->slug)
            ->where('tenant_id', $this->tenantProjectProvider->getCurrentTenantId()->toString())
            ->where('project_id', $this->tenantProjectProvider->getCurrentProjectId()->toString())
            ->first();

        if (! $content) {
            return null;
        }

        return $this->getContentWithBlocks($content);
    }

    public function getContentWithBlocks(ContentModel $content): ContentModel
    {

        // If there are block IDs in custom_fields
        if (! empty($content->custom_fields['blocks'])) {
            // Fetch all related blocks
            $blocks = ContentModel::query()
                ->where('type', 'blocks')
                ->whereIn('id', $content->custom_fields['blocks'])
                ->get();

            $content->custom_fields = array_merge($content->custom_fields, [
                'blocks' => (new ContentResource($content))->collection($blocks),
            ]);

            return $content;

        }

        return $content;
    }
}
