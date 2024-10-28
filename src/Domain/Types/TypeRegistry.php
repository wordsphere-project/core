<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Types;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Domain\Types\Entities\Type;
use WordSphere\Core\Domain\Types\Repositories\TypeRepositoryInterface;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;

use function array_key_exists;

class TypeRegistry
{
    private array $registeredTypes = [];

    private array $registrationInProgress = [];

    private TypeRepositoryInterface $repository;

    private TenantProjectProvider $contextProvider;

    public function __construct(
        TypeRepositoryInterface $repository,
        TenantProjectProvider $contextProvider
    ) {
        $this->repository = $repository;
        $this->contextProvider = $contextProvider;
    }

    public function get(TypeKey $key): ?Type
    {
        $tenantId = $this->contextProvider->getCurrentTenantId();
        $projectId = $this->contextProvider->getCurrentProjectId();

        $contextKey = $this->getContextKey($key, $tenantId, $projectId);

        if (! array_key_exists($contextKey, $this->registeredTypes)) {
            $type = $this->repository->findByKey($key, $tenantId, $projectId);
            if ($type) {
                $this->registeredTypes[$contextKey] = $type;
            }
        }

        $type = $this->registeredTypes[$contextKey];

        return $type;
    }

    public function all(): array
    {
        $tenantId = $this->contextProvider->getCurrentTenantId();
        $projectId = $this->contextProvider->getCurrentProjectId();

        $types = $this->repository->findAll($tenantId, $projectId);

        foreach ($types as $type) {
            $contextKey = $this->getContextKey(
                $type->getKey(),
                $type->getTenantId(),
                $type->getProjectId()
            );
            $this->registeredTypes[$contextKey] = $type;
        }

        return $types;
    }

    public function update(Type $type): void
    {
        $contextKey = $this->getContextKey(
            $type->getKey(),
            $type->getTenantId(),
            $type->getProjectId()
        );

        // Update in repository
        $this->repository->save($type);

        // Update in-memory cache
        $this->registeredTypes[$contextKey] = $type;
    }

    public function register(TypeKey $key, string $entityClass, ?array $interfaceData = null): Type
    {
        // Prevent circular registration
        if (isset($this->registrationInProgress[$key->toString()])) {

            throw new \RuntimeException('Circular type registration detected for key:'.$key->toString());
        }

        $this->registrationInProgress[$key->toString()] = true;

        try {
            $type = $this->doRegister($key, $entityClass, $interfaceData);
        } finally {
            unset($this->registrationInProgress[$key->toString()]);
        }

        return $type;
    }

    public function doRegister(TypeKey $key, string $entityClass, ?array $interfaceData = null): Type
    {
        $tenantId = $this->contextProvider->getCurrentTenantId();
        $projectId = $this->contextProvider->getCurrentProjectId();
        $contextKey = $this->getContextKey($key, $tenantId, $projectId);

        // Check if already registered in memory
        if (isset($this->registeredTypes[$contextKey])) {
            return $this->registeredTypes[$contextKey];
        }

        try {
            $type = $this->repository->findByKey($key, $tenantId, $projectId);
            if ($type) {
                if ($interfaceData) {
                    $type->addInterfaceData($interfaceData);
                    $this->repository->save($type);
                }
            } else {
                $type = new Type(
                    id: Uuid::generate(),
                    key: $key,
                    entityClass: $entityClass,
                    tenantId: $tenantId,
                    projectId: $projectId
                );

                if ($interfaceData) {
                    $type->addInterfaceData($interfaceData);
                }

                $this->repository->save($type);
            }

            $this->registeredTypes[$contextKey] = $type;

            return $type;

        } finally {
            unset($this->registrationInProgress[$key->toString()]);
        }
    }

    private function getContextKey(TypeKey $key, Uuid $tenantId, Uuid $projectId): string
    {
        return implode(':', [
            'type',
            $key->toString(),
            'tenant',
            $tenantId->toString(),
            'project',
            $projectId->toString(),
        ]);
    }
}
