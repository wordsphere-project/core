<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use WordSphere\Core\Application\ContentManagement\Queries\GetContentBySlugQuery;
use WordSphere\Core\Application\ContentManagement\Queries\GetContentsByTypeQuery;
use WordSphere\Core\Infrastructure\ContentManagement\Cache\ContentCacheManager;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;

readonly class CachedContentQueryService
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private ContentQueryService $contentQueryService,
        private TenantProjectProvider $tenantProjectProvider,
        private ContentCacheManager $cacheManager,
        private Cache $cache
    ) {}

    public function getContentsByType(GetContentsByTypeQuery $query): LengthAwarePaginator
    {
        $tenantId = $this->tenantProjectProvider->getCurrentTenantId()->toString();
        $projectId = $this->tenantProjectProvider->getCurrentProjectId()->toString();

        $cacheKey = $this->cacheManager->getListCacheKey(
            type: $query->type->toString(),
            tenantId: $tenantId,
            projectId: $projectId,
            page: $query->page,
            perPage: $query->perPage,
            orderBy: $query->orderBy,
            orderDirection: $query->orderDirection,
        );

        return $this->cache->remember($cacheKey, self::CACHE_TTL, function () use ($query) {
            $result = $this->contentQueryService->getContentsByType($query);

            $this->cacheManager->updatePaginationMeta(
                type: $query->type->toString(),
                tenantId: $this->tenantProjectProvider->getCurrentTenantId()->toString(),
                projectId: $this->tenantProjectProvider->getCurrentProjectId()->toString(),
                lastPage: $result->lastPage(),
                meta: [
                    'total' => $result->total(),
                    'perPage' => $result->perPage(),
                ]
            );

            return $result;

        });
    }

    public function getContentBySlug(GetContentBySlugQuery $query): ?ContentModel
    {
        $key = sprintf(
            'content:single:%s:%s:%s:%s',
            $this->tenantProjectProvider->getCurrentTenantId()->toString(),
            $this->tenantProjectProvider->getCurrentProjectId()->toString(),
            $query->type->toString(),
            $query->slug
        );

        return $this->cache->remember($key, ContentCacheManager::DEFAULT_TTL, function () use ($query) {
            return $this->contentQueryService->getContentBySlug($query);
        });
    }

    private function buildSlugCacheKey(string $type, string $slug): string
    {
        return sprintf(
            'content:slug:%s:%s:%s:%s',
            $this->tenantProjectProvider->getCurrentTenantId()->toString(),
            $this->tenantProjectProvider->getCurrentProjectId()->toString(),
            $type,
            $slug
        );
    }

    public function invalidateContentCache(string $type, ?string $slug = null): void
    {
        if ($slug) {
            $this->cache->forget($this->buildSlugCacheKey($type, $slug));
        }

        // Also invalidate list cache as content might have changed
        $this->cache->tags(['content:list', "content:type:$type"])->flush();
    }
}
