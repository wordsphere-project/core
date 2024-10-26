<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Types\Events;

use WordSphere\Core\Domain\Shared\Contracts\DomainEvent;
use WordSphere\Core\Domain\Types\ValueObjects\AllowedRelation;

readonly class AllowedRelationAdded implements DomainEvent
{
    public function __construct(
        public AllowedRelation $relation
    ) {}
}
