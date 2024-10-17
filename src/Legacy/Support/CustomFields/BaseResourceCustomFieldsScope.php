<?php

declare(strict_types=1);

namespace WordSphere\Core\Legacy\Support\CustomFields;

final class BaseResourceCustomFieldsScope extends CustomFieldsScope
{
    public static function make(): self
    {
        return new self(
            key: 'scope',
            value: 'all'
        );
    }
}
