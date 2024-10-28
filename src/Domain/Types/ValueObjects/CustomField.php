<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Types\ValueObjects;

readonly class CustomField
{
    public function __construct(
        private string $key,
        private string $type,
        private array $config = [],
        private array $validation = [],
        private array $dependencies = [],
    ) {}

    public function getKey(): string
    {
        return $this->key;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getValidation(): array
    {
        return $this->validation;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'type' => $this->type,
            'config' => $this->config,
            'validation' => $this->validation,
            'dependencies' => $this->dependencies,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            key: $data['key'],
            type: $data['type'],
            config: $data['config'],
            validation: $data['validation'] ?? [],
            dependencies: $data['dependencies'] ?? []
        );
    }
}
