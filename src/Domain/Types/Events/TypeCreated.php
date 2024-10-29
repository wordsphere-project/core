<?php

namespace WordSphere\Core\Domain\Types\Events;

use WordSphere\Core\Domain\Shared\Contracts\DomainEvent;
use WordSphere\Core\Domain\Types\Entities\Type;

readonly class TypeCreated implements DomainEvent
{
    public function __construct(
        public Type $type
    ) {}
}
