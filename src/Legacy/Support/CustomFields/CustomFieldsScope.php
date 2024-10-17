<?php

declare(strict_types=1);

namespace WordSphere\Core\Legacy\Support\CustomFields;

class CustomFieldsScope
{
    public function __construct(
        public string $key,
        public string $value
    ) {}
}
