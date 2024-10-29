<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Types\Services;

use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;

class EntityModelMapper
{
    private array $mappings = [
        Content::class => ContentModel::class,
    ];

    public function getModelClass(string $entityClass): string
    {
        if (! isset($this->mappings[$entityClass])) {
            throw new \InvalidArgumentException("No model mapping found for entity class: {$entityClass}");
        }

        return $this->mappings[$entityClass];
    }
}
