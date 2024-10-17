<?php

namespace WordSphere\Core\Legacy\Support\CustomFields;

use Illuminate\Support\Collection;

/**
 * @property ?Collection<int, CustomFieldsRegistration> $items
 */
readonly class CustomFieldsContainer
{
    public function __construct(
        public ?Collection $items
    ) {}
}
