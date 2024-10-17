<?php

declare(strict_types=1);

namespace WordSphere\Core\Legacy\Support\CustomFields;

use Filament\Forms\Components\Field;
use Illuminate\Support\Collection;

class CustomFieldsRegistration
{
    /**
     * @param  Collection<int, Field>  $fields
     */
    public function __construct(
        public string $resource,
        public string $tab,
        public CustomFieldsScope $scope,
        public Collection $fields,
    ) {}
}
