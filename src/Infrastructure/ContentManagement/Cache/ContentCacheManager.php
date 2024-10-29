<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ContentManagement\Cache;

use Illuminate\Cache\Repository as Cache;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;

class ContentCacheManager
{
    private const CACHE_PREFIX = 'content';

    public const DEFAULT_TTL = 3600;

    public function __construct(
        private readonly Cache $cache
    ) {}

    public function invalidateContent(ContentModel $content): void
    {

        // Clear specific content cache
        $this->cache->forget($this->getContentKey($content));

        // Clear type listing cache
        $this->cache->forget($this->getTypeListKey($content->type, $content->tenant_id, $content->project_id));

        // Clear tenant's content list cache
        $this->cache->forget($this->getTenantListKey($content->tenant_id, $content->project_id));

        // Clear list cache for all possible page combinations
        $this->invalidateListCache($content);

        // Clear related content caches
        $this->invalidateRelatedContent($content);

    }

    private function invalidateListCache(ContentModel $content): void
    {
        // We'll store the last known pagination values in a separate cache entry
        $paginationKey = $this->getPaginationMetaKey($content->type, $content->tenant_id, $content->project_id);
        $paginationMeta = $this->cache->get($paginationKey, [
            'lastPage' => 1,
            'perPageOptions' => [10, 25, 50],
            'orderByOptions' => ['created_at', 'updated_at', 'title'],
            'orderDirections' => ['asc', 'desc'],
        ]);

        // Invalidate all possible pagination combinations
        for ($page = 1; $page <= $paginationMeta['lastPage']; $page++) {
            foreach ($paginationMeta['perPageOptions'] as $perPage) {
                foreach ($paginationMeta['orderByOptions'] as $orderBy) {
                    foreach ($paginationMeta['orderDirections'] as $direction) {
                        $key = $this->getListCacheKey(
                            type: $content->type,
                            tenantId: $content->tenant_id,
                            projectId: $content->project_id,
                            page: $page,
                            perPage: $perPage,
                            orderBy: $orderBy,
                            orderDirection: $direction
                        );
                        $this->cache->forget($key);
                    }
                }
            }
        }
    }

    private function invalidateRelatedContent(ContentModel $content): void
    {
        $relatedTypes = $this->getRelatedTypes($content);

        foreach ($relatedTypes as $relatedType) {
            $relatedKey = $this->getTypeListKey($relatedType, $content->tenant_id, $content->project_id);
            $this->cache->forget($relatedKey);

            // Also invalidate pagination meta for related types
            $this->cache->forget($this->getPaginationMetaKey($relatedType, $content->tenant_id, $content->project_id));
        }
    }

    public function updatePaginationMeta(
        string $type,
        string $tenantId,
        string $projectId,
        int $lastPage,
        array $meta = []
    ): void {
        $key = $this->getPaginationMetaKey($type, $tenantId, $projectId);
        $this->cache->put($key, array_merge([
            'lastPage' => $lastPage,
            'perPageOptions' => [10, 25, 50],
            'orderByOptions' => ['created_at', 'updated_at', 'title'],
            'orderDirections' => ['asc', 'desc'],
        ], $meta), self::DEFAULT_TTL);
    }

    public function getListCacheKey(
        string $type,
        string $tenantId,
        string $projectId,
        int $page = 1,
        int $perPage = 10,
        string $orderBy = 'created_at',
        string $orderDirection = 'desc'
    ): string {
        return sprintf(
            '%s:list:%s:%s:%s:%d:%d:%s:%s',
            self::CACHE_PREFIX,
            $tenantId,
            $projectId,
            $type,
            $page,
            $perPage,
            $orderBy,
            $orderDirection
        );
    }

    private function getContentKey(ContentModel $content): string
    {
        return sprintf(
            '%s:single:%s:%s:%s:%s',
            self::CACHE_PREFIX,
            $content->tenant_id,
            $content->project_id,
            $content->type,
            $content->slug
        );
    }

    private function getTypeListKey(string $type, string $tenantId, string $projectId): string
    {
        return sprintf(
            '%s:type_list:%s:%s:%s',
            self::CACHE_PREFIX,
            $tenantId,
            $projectId,
            $type
        );
    }

    private function getTenantListKey(string $tenantId, string $projectId): string
    {
        return sprintf(
            '%s:tenant_list:%s:%s',
            self::CACHE_PREFIX,
            $tenantId,
            $projectId
        );
    }

    private function getPaginationMetaKey(string $type, string $tenantId, string $projectId): string
    {
        return sprintf(
            '%s:pagination_meta:%s:%s:%s',
            self::CACHE_PREFIX,
            $tenantId,
            $projectId,
            $type
        );
    }

    private function getRelatedTypes(ContentModel $content): array
    {
        return match ($content->type) {
            'pages' => ['blocks', 'featuredImage', 'media'],
            'blocks' => ['featuredImage', 'media'],
            default => []
        };
    }
}
