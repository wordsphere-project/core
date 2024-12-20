<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Types\Repositories;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Domain\Types\Entities\Type;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;

interface TypeRepositoryInterface
{
    public function findByKey(TypeKey $key, Uuid $tenantId, Uuid $projectId): ?Type;

    public function findById(Uuid $id, Uuid $tenantId, Uuid $projectId): ?Type;

    public function findAll(Uuid $tenantId, Uuid $projectId): array;

    public function save(Type $type): void;

    public function delete(Type $type): void;
}
