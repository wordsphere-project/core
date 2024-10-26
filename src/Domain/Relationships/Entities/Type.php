<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Relationships\Entities;

use InvalidArgumentException;
use WordSphere\Core\Domain\Relationships\Contracts\TypeableInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

class Type
{
    /** @phpstan-ignore-next-line  */
    private array $allowedRelations = [];

    public function __construct(
        private readonly Uuid $id,
        private readonly string $key,
        private readonly string $entityClass,
    ) {
        $this->validateKey($key);
        $this->validateEntityClass($entityClass);
    }

    private function validateKey(string $key): void
    {
        if (! preg_match('/^[a-z0-9_]+$/', $key)) {
            throw new InvalidArgumentException(
                'Type key must contain only lowercase letters, numbers, and underscores'
            );
        }
    }

    private function validateEntityClass(string $entityClass): void
    {
        if (! class_exists($entityClass)) {
            throw new InvalidArgumentException("Entity class {$entityClass} does not exist");
        }

        $interfaces = class_implements($entityClass);
        if (! isset($interfaces[TypeableInterface::class])) {
            throw new InvalidArgumentException('Entity class must implement TypeableInterface');
        }
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}
