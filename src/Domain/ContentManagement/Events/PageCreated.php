<?php

namespace WordSphere\Core\Domain\ContentManagement\Events;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

class PageCreated
{
    public function __construct(
        public Uuid $id
    ) {}
}
