<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Types\Persistence\Cache;

use Illuminate\Cache\Repository as Cache;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Domain\Types\Entities\Type;
use WordSphere\Core\Domain\Types\TypeRepositoryInterface;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use function count;

readonly class CachedTypeRepository implements TypeRepositoryInterface
{
    private const CACHE_TTL = 3600;

    private const CACHE_PREFIX = 'types';

    public function __construct(
        private TypeRepositoryInterface $repository,
        private Cache $cache
    ) {}

    private function getCacheKey(string $identifier, string $type, Uuid $tenantId, Uuid $projectId): string
    {
        return sprintf(
            '%s:%s:%s:tenant:%s:project:%s',
            self::CACHE_PREFIX,
            $type,
            $identifier,
            $tenantId->toString(),
            $projectId->toString()
        );
    }

    private function getCacheKeys(Type $type): array
    {
        return [
            $this->getCacheKey($type->getId()->toString(), 'id', $type->getTenantId(), $type->getProjectId()),
            $this->getCacheKey($type->getKey()->toString(), 'key', $type->getTenantId(), $type->getProjectId()),
            $this->getCacheKey('list', 'all', $type->getTenantId(), $type->getProjectId()),
        ];
    }

    public function findById(Uuid $id, Uuid $tenantId, Uuid $projectId): ?Type
    {
        $cacheKey = $this->getCacheKey($id->toString(), 'id', $tenantId, $projectId);

        return $this->cache->remember(
            key: $cacheKey,
            ttl: self::CACHE_TTL,
            callback: function () use ($id, $tenantId, $projectId) {
                return $this->repository->findById($id, $tenantId, $projectId);
            }
        );
    }

    public function findByKey(TypeKey $key, Uuid $tenantId, Uuid $projectId): ?Type
    {
        $cacheKey = $this->getCacheKey($key->toString(), 'key', $tenantId, $projectId);

        return $this->cache->remember(
            key: $cacheKey,
            ttl: self::CACHE_TTL,
            callback: function () use ($key, $tenantId, $projectId) {
                return $this->repository->findByKey($key, $tenantId, $projectId);
            }
        );
    }

    public function save(Type $type): void
    {
        $this->repository->save($type);
        $this->clearTypeCache($type);

    }

    public function delete(Type $type): void
    {
        $this->repository->delete($type);
        $this->clearTypeCache($type);
    }

    private function clearTypeCache(Type $type): void
    {
        foreach ($this->getCacheKeys($type) as $cacheKey) {
            $this->cache->forget($cacheKey);
        }
    }

    /**
     * Helper method to clear all types cache for a tenant/project
     */
    public function clearAllTypesCache(Uuid $tenantId, Uuid $projectId): void
    {
        $pattern = sprintf(
            '%s:*:*:tenant:%s:project:%s',
            self::CACHE_PREFIX,
            $tenantId->toString(),
            $projectId->toString()
        );

        if (config('cache.default') === 'redis') {
            // For Redis, we can use scan to find and delete keys
            $this->clearRedisCache($pattern);
        } else {
            // For other drivers, we might need to maintain a list of keys
            $this->clearGenericCache($tenantId, $projectId);
        }
    }

    private function clearRedisCache(string $pattern): void
    {

        /** @phpstan-ignore-next-line  */
        $redis = $this->cache->getStore()->getRedis();

        $iterator = null;
        do {
            $keys = $redis->scan($iterator, [
                'match' => $pattern,
                'count' => 100,
            ]);

            if ($keys) {
                foreach ($keys as $key) {
                    $this->cache->forget($key);
                }
            }
            /** @phpstan-ignore-next-line  */
        } while ($iterator > 0);
    }

    private function clearGenericCache(Uuid $tenantId, Uuid $projectId): void
    {
        // For non-Redis caches, maintain a list of cached keys
        $keysListCacheKey = sprintf(
            '%s:keys:tenant:%s:project:%s',
            self::CACHE_PREFIX,
            $tenantId->toString(),
            $projectId->toString()
        );

        $cachedKeys = $this->cache->get($keysListCacheKey, []);

        if(count($cachedKeys) > 0) {
            foreach ($cachedKeys as $key) {
                $this->cache->forget($key);
            }
        }



        $this->cache->forget($keysListCacheKey);
    }

    /**
     * Track a cached key for non-Redis stores
     *
     * @phpstan-ignore-next-line
     */
    private function trackCacheKey(string $key, Uuid $tenantId, Uuid $projectId): void
    {
        if (config('cache.default') !== 'redis') {
            $keysListCacheKey = sprintf(
                '%s:keys:tenant:%s:project:%s',
                self::CACHE_PREFIX,
                $tenantId->toString(),
                $projectId->toString()
            );

            $cachedKeys = $this->cache->get($keysListCacheKey, []);
            $cachedKeys[] = $key;
            $this->cache->put($keysListCacheKey, array_unique($cachedKeys), self::CACHE_TTL);
        }
    }
}
